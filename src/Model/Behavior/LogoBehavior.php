<?php
declare(strict_types=1);

namespace eurokeep\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use DOMDocument;
use finfo;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Throwable;

/**
 * I manage exactly one uploaded logo (image) per entity.
 *
 * The logo column holds either a static frontend asset (no prefix) or, prefixed with
 * LogoBehavior::UPLOAD_PREFIX, the name of an uploaded file inside the configured `directory`.
 * Uploaded files are named `<id>-<random>.<ext>`, a new upload replaces the previous file.
 *
 * Config:
 * - `field`: the column holding the logo, e.g. 'logo' or 'image'.
 * - `directory`: absolute path of the directory uploaded logos are stored in.
 */
class LogoBehavior extends Behavior
{
    public const string UPLOAD_PREFIX = 'upload/';
    public const int MAX_BYTES = 2 * 1024 * 1024;

    public const array MIME_TYPES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];

    protected array $_defaultConfig = [
        'field' => 'logo',
        'directory' => null,
        'implementedMethods' => [
            'getLogoDirectory' => 'getLogoDirectory',
            'getLogoPath' => 'getLogoPath',
            'storeLogo' => 'storeLogo',
            'removeLogo' => 'removeLogo',
        ],
    ];

    /**
     * I will remove the uploaded logo of a deleted entity.
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity): void
    {
        $this->removeLogoFiles((int)$entity->get('id'));
    }

    /**
     * I am the directory uploaded logos are stored in.
     */
    public function getLogoDirectory(): string
    {
        return (string)$this->getConfig('directory');
    }

    /**
     * I will return the absolute path of the uploaded logo of the given $entity.
     * @return string|null I am null if the entity has no uploaded logo (or its file is gone).
     */
    public function getLogoPath(EntityInterface $entity): ?string
    {
        $logo = (string)$entity->get($this->getConfig('field'));
        if (!str_starts_with($logo, self::UPLOAD_PREFIX)) {
            return null;
        }

        $path = $this->getLogoDirectory() . DS . basename(substr($logo, strlen(self::UPLOAD_PREFIX)));

        return is_file($path) ? $path : null;
    }

    /**
     * I will store the given $file as the one and only logo of the given $entity.
     * A previously uploaded logo of the entity is replaced.
     *
     * @throws InvalidArgumentException If the file is missing, too large or not an image.
     * @throws RuntimeException If the file cannot be stored or the entity cannot be saved.
     */
    public function storeLogo(EntityInterface $entity, UploadedFileInterface $file): EntityInterface
    {
        match ($file->getError()) {
            UPLOAD_ERR_OK => null,
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => throw new InvalidArgumentException('The logo is too large (max. 2 MB).'),
            UPLOAD_ERR_NO_FILE => throw new InvalidArgumentException('No logo uploaded.'),
            default => throw new RuntimeException('The logo upload failed.'),
        };

        if ((int)$file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('The logo is too large (max. 2 MB).');
        }

        $directory = $this->getLogoDirectory();
        if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) {
            throw new RuntimeException('Cannot create the logo directory.');
        }

        // Move first and inspect the stored file: the client-supplied name and type are never trusted.
        $tmpPath = $directory . DS . '.upload-' . bin2hex(random_bytes(8));
        try {
            $file->moveTo($tmpPath);
        } catch (Throwable $exception) {
            throw new RuntimeException('Cannot store the logo.', 0, $exception);
        }

        $mimeType = $this->detectMimeType($tmpPath);
        if ($mimeType === null) {
            unlink($tmpPath);
            throw new InvalidArgumentException('Only images (PNG, JPEG, GIF, WebP, SVG) can be uploaded as logo.');
        }

        // A new file name per upload, so browsers never show a cached old logo.
        $id = (int)$entity->get('id');
        $fileName = sprintf('%d-%s.%s', $id, bin2hex(random_bytes(4)), self::MIME_TYPES[$mimeType]);
        if (!rename($tmpPath, $directory . DS . $fileName)) {
            unlink($tmpPath);
            throw new RuntimeException('Cannot store the logo.');
        }

        $entity->set($this->getConfig('field'), self::UPLOAD_PREFIX . $fileName);
        if (!$this->table()->save($entity)) {
            unlink($directory . DS . $fileName);
            throw new RuntimeException('Cannot save the logo.');
        }

        // Only now that the new logo is in place, drop the previous one.
        $this->removeLogoFiles($id, $fileName);

        return $entity;
    }

    /**
     * I will remove the logo of the given $entity. An uploaded logo file is deleted,
     * static frontend assets are only unlinked (they may be used by other entities).
     *
     * @throws RuntimeException If the entity cannot be saved.
     */
    public function removeLogo(EntityInterface $entity): EntityInterface
    {
        $entity->set($this->getConfig('field'), null);
        if (!$this->table()->save($entity)) {
            throw new RuntimeException('Cannot remove the logo.');
        }

        $this->removeLogoFiles((int)$entity->get('id'));

        return $entity;
    }

    /**
     * I will detect the real MIME type of the image at $path.
     * @return string|null I am one of the keys of MIME_TYPES, or null if the file is no accepted image.
     */
    private function detectMimeType(string $path): ?string
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if ($mimeType !== 'image/svg+xml' && isset(self::MIME_TYPES[$mimeType])) {
            return getimagesize($path) !== false ? $mimeType : null;
        }

        // libmagic reports SVGs without XML declaration as text/xml or text/plain.
        if (in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/xml', 'text/plain', 'text/html'], true)) {
            $document = new DOMDocument();
            $loaded = @$document->loadXML((string)file_get_contents($path), LIBXML_NONET);
            // A plain <!DOCTYPE svg PUBLIC ...> is fine, own entity declarations (internal subset) are not.
            $hasEntities = trim((string)$document->doctype?->internalSubset) !== '';
            if ($loaded && !$hasEntities && $document->documentElement?->localName === 'svg') {
                return 'image/svg+xml';
            }
        }

        return null;
    }

    /**
     * I will delete all uploaded logo files of the entity with the given $id, except the one named $keep.
     */
    private function removeLogoFiles(int $id, ?string $keep = null): void
    {
        foreach (glob($this->getLogoDirectory() . DS . $id . '-*') ?: [] as $path) {
            if (basename($path) !== $keep && is_file($path)) {
                unlink($path);
            }
        }
    }
}
