<?php
declare(strict_types=1);

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Validation\Validator;
use DOMDocument;
use eurokeep\Model\Entity\Subscription;
use finfo;
use InvalidArgumentException;
use Override;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Throwable;

/**
 * Subscription Model
 *
 * @method Subscription newEmptyEntity()
 * @method Subscription newEntity(array $data, array $options = [])
 * @method Subscription[] newEntities(array $data, array $options = [])
 * @method Subscription get($primaryKey, $options = [])
 * @method Subscription findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Subscription patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Subscription[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Subscription|false save(EntityInterface $entity, $options = [])
 * @method Subscription saveOrFail(EntityInterface $entity, $options = [])
 * @method Subscription[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class SubscriptionTable extends OwnedTable
{
    public const string LOGO_UPLOAD_PREFIX = 'upload/';
    public const int LOGO_MAX_BYTES = 2 * 1024 * 1024;

    public const array LOGO_MIME_TYPES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    #[Override]
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('subscription');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
        ]);

        $this->belongsTo('Account', [
            'foreignKey' => 'account_id',
        ]);

        $this->belongsTo('PaymentMethod', [
            'foreignKey' => 'paymentmethod_id',
        ]);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     */
    #[Override]
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('flags')
            ->notEmptyString('flags');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('account_id')
            ->requirePresence('account_id', 'create')
            ->notEmptyString('account_id');

        $validator
            ->numeric('balance_value')
            ->requirePresence('balance_value', 'create')
            ->notEmptyString('balance_value');

        $validator
            ->integer('category_id')
            ->allowEmptyString('category_id');

        $validator
            ->integer('paymentmethod_id')
            ->allowEmptyString('paymentmethod_id');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('interval')
            ->maxLength('interval', 128)
            ->allowEmptyString('interval');

        $validator
            ->date('start')
            ->allowEmptyDate('start');

        $validator
            ->date('end')
            ->allowEmptyDate('end');

        $validator
            ->scalar('term')
            ->maxLength('term', 128)
            ->allowEmptyString('term');

        $validator
            ->scalar('noticeperiod')
            ->maxLength('noticeperiod', 128)
            ->allowEmptyString('noticeperiod');

        $validator
            ->scalar('comments')
            ->maxLength('comments', 512)
            ->allowEmptyString('comments');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        return parent::validationDefault($validator);
    }

    /**
     * I will remove the uploaded logo of a deleted subscription.
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity): void
    {
        $this->removeLogoFiles((int)$entity->get('id'));
    }

    /**
     * I am the directory uploaded subscription logos are stored in.
     */
    public function getLogoDirectory(): string
    {
        return ROOT . DS . 'uploads' . DS . 'subscription-logos';
    }

    /**
     * I will return the absolute path of the uploaded logo of the given $subscription.
     * @return string|null I am null if the subscription has no uploaded logo (or its file is gone).
     */
    public function getLogoPath(Subscription $subscription): ?string
    {
        $logo = (string)$subscription->logo;
        if (!str_starts_with($logo, self::LOGO_UPLOAD_PREFIX)) {
            return null;
        }

        $path = $this->getLogoDirectory() . DS . basename(substr($logo, strlen(self::LOGO_UPLOAD_PREFIX)));

        return is_file($path) ? $path : null;
    }

    /**
     * I will store the given $file as the one and only logo of the given $subscription.
     * A previously uploaded logo of the subscription is replaced.
     *
     * @throws InvalidArgumentException If the file is missing, too large or not an image.
     * @throws RuntimeException If the file cannot be stored or the subscription cannot be saved.
     */
    public function storeLogo(Subscription $subscription, UploadedFileInterface $file): Subscription
    {
        match ($file->getError()) {
            UPLOAD_ERR_OK => null,
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => throw new InvalidArgumentException('The logo is too large (max. 2 MB).'),
            UPLOAD_ERR_NO_FILE => throw new InvalidArgumentException('No logo uploaded.'),
            default => throw new RuntimeException('The logo upload failed.'),
        };

        if ((int)$file->getSize() > self::LOGO_MAX_BYTES) {
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

        $mimeType = $this->detectLogoMimeType($tmpPath);
        if ($mimeType === null) {
            unlink($tmpPath);
            throw new InvalidArgumentException('Only images (PNG, JPEG, GIF, WebP, SVG) can be uploaded as logo.');
        }

        // A new file name per upload, so browsers never show a cached old logo.
        $fileName = sprintf('%d-%s.%s', $subscription->id, bin2hex(random_bytes(4)), self::LOGO_MIME_TYPES[$mimeType]);
        if (!rename($tmpPath, $directory . DS . $fileName)) {
            unlink($tmpPath);
            throw new RuntimeException('Cannot store the logo.');
        }

        $subscription->set('logo', self::LOGO_UPLOAD_PREFIX . $fileName);
        if (!$this->save($subscription)) {
            unlink($directory . DS . $fileName);
            throw new RuntimeException('Cannot save the subscription.');
        }

        // Only now that the new logo is in place, drop the previous one.
        $this->removeLogoFiles((int)$subscription->id, $fileName);

        return $subscription;
    }

    /**
     * I will remove the logo of the given $subscription. An uploaded logo file is deleted,
     * static frontend assets are only unlinked (they may be used by other subscriptions).
     *
     * @throws RuntimeException If the subscription cannot be saved.
     */
    public function removeLogo(Subscription $subscription): Subscription
    {
        $subscription->set('logo', null);
        if (!$this->save($subscription)) {
            throw new RuntimeException('Cannot save the subscription.');
        }

        $this->removeLogoFiles((int)$subscription->id);

        return $subscription;
    }

    /**
     * I will detect the real MIME type of the image at $path.
     * @return string|null I am one of the keys of LOGO_MIME_TYPES, or null if the file is no accepted image.
     */
    private function detectLogoMimeType(string $path): ?string
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if ($mimeType !== 'image/svg+xml' && isset(self::LOGO_MIME_TYPES[$mimeType])) {
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
     * I will delete all uploaded logo files of the subscription with the given $subscriptionId,
     * except the one named $keep.
     */
    private function removeLogoFiles(int $subscriptionId, ?string $keep = null): void
    {
        foreach (glob($this->getLogoDirectory() . DS . $subscriptionId . '-*') ?: [] as $path) {
            if (basename($path) !== $keep && is_file($path)) {
                unlink($path);
            }
        }
    }
}
