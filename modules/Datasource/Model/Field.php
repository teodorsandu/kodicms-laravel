<?php namespace KodiCMS\Datasource\Model;

use DB;
use FieldManager;
use KodiCMS\Datasource\FieldType;
use Illuminate\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use KodiCMS\Support\Traits\ModelSettings;
use KodiCMS\Datasource\Contracts\FieldInterface;
use KodiCMS\Datasource\Exceptions\FieldException;
use KodiCMS\Datasource\Contracts\SectionInterface;
use KodiCMS\Datasource\Contracts\DocumentInterface;

class Field extends Model implements FieldInterface
{
	use ModelSettings;

	// TODO: вынести в отдельный Observer
	protected static function boot()
	{
		parent::boot();

		static::creating(function(Field $field)
		{
			$field->position = $field->getLastPosition() + 1;
		});
	}

	/**
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * @var array
	 */
	protected $fieldSettings = [];

	/**
	 * @var string
	 */
	protected $table = 'datasource_fields';

	/**
	 * @var FieldType
	 */
	protected $fieldType;

	/**
	 * @var null|SectionInterface
	 */
	protected $relatedSection = null;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'ds_id', 'key', 'type', 'name', 'related_ds',
		'is_system', 'position', 'settings'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'ds_id' => 'integer',
		'key' => 'string',
		'type' => 'string',
		'name' => 'string',
		'related_ds' => 'integer',
		'is_system' => 'boolean',
		'position' => 'integer',
		'settings' => 'array'
	];

	/**
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		if (empty($attributes['type']))
		{
			$attributes['type'] = FieldManager::getTypeByClassName(get_called_class());
		}

		parent::__construct($attributes);
	}

	/**
	 * @return array
	 */
	public function defaultSettings()
	{
		return [];
	}

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * @param string $tablePrefix
	 */
	public function setTablePrefix($tablePrefix)
	{
		$this->tablePrefix = $tablePrefix;
	}

	/**
	 * @return string
	 */
	public function getDBKey()
	{
		return $this->getTablePrefix() . $this->getKey();
	}

	/**
	 * @return bool
	 */
	public function isSystem()
	{
		return (bool) $this->is_system;
	}

	/**
	 * @return bool
	 */
	public function isRequire()
	{
		return $this->getSetting('is_required', false);
	}

	/**
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->getSetting('is_visible', false);
	}

	/**
	 * @param bool $status
	 */
	public function setVisibleStatus($status)
	{
		$this->setSetting('is_visible', (bool) $status);
	}

	/**
	 * @return bool
	 */
	public function isSearchable()
	{
		return $this->getSetting('is_searchable', false);
	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->getSetting('default_value', false);
	}

	/**************************************************************************
	 * Type
	 **************************************************************************/
	/**
	 * @return \KodiCMS\Datasource\FieldType|null
	 * @throws FieldException
	 */
	public function getType()
	{
		if ($this->fieldType)
		{
			return $this->fieldType;
		}

		if (is_null($typeObject = FieldManager::getFieldTypeBy('type', $this->type)))
		{
			throw new FieldException("Datasource field type {$this->type} not found");
		}

		return $this->fieldType = $typeObject;
	}

	/**
	 * @return string
	 */
	public function getTypeTitle()
	{
		return $this->getType()->getTitle();
	}

	/**************************************************************************
	 * Converting
	 **************************************************************************/
	/**
	 * @param mixed $value
	 * @return string
	 */
	public function convertValueToHTML($value)
	{
		return $value;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	public function convertValueToSQL($value)
	{
		return $value;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	public function convertValueToHeadline($value)
	{
		return $value;
	}

	/**************************************************************************
	 * Events
	 **************************************************************************/

	/**
	 * @param DocumentInterface $document
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function onSetDocumentAttribute(DocumentInterface $document, $value)
	{
		return $value;
	}

	/**
	 * @param DocumentInterface $document
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function onGetDocumentAttribute(DocumentInterface $document, $value)
	{
		return $value;
	}

	/**
	 * @param DocumentInterface $document
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function onGetFormAttributeValue(DocumentInterface $document, $value)
	{
		return $value;
	}

	/**
	 * @param DocumentInterface $document
	 * @param Validator $validator
	 * @param $value
	 */
	public function onValidateDocument(DocumentInterface $document, Validator $validator, $value)
	{

	}

	/**
	 * @param DocumentInterface $document
	 * @param $value
	 */
	public function onDocumentCreate(DocumentInterface $document, $value)
	{

	}

	/**
	 * @param DocumentInterface $oldDocument
	 * @param DocumentInterface $document
	 * @param $value
	 */
	public function onDocumentUpdate(DocumentInterface $oldDocument, DocumentInterface $document, $value)
	{

	}

	/**
	 * @param DocumentInterface $document
	 */
	public function onDocumentRemove(DocumentInterface $document)
	{

	}

	/**************************************************************************
	 * Database
	 **************************************************************************/
	/**
	 * @param Builder $query
	 */
	public function querySelectColumn(Builder $query)
	{
		$query->selectRaw("{$this->getDBKey()} as {$this->getKey()}");
	}

	/**
	 * @param Builder $query
	 * @param string $dir
	 */
	public function queryOrderBy(Builder $query, $dir = 'asc')
	{
		$query->orderBy($this->getKey(), $dir);
	}

	/**
	 * @param Builder $query
	 * @param $condition
	 * @param $value
	 */
	public function queryWhereCondition(Builder $query, $condition, $value)
	{
		$query->where($this->getKey(), $condition, $value);
	}

	/**
	 * @param Blueprint $table
	 */
	public function setDatabaseFieldType(Blueprint $table)
	{
		$table->string($this->getDBKey());
	}

	/**************************************************************************
	 * Relations
	 **************************************************************************/
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function section()
	{
		return $this->belongsTo('KodiCMS\Datasource\Model\Section', 'ds_id');
	}

	/**************************************************************************
	 * Other
	 **************************************************************************/
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function setSetting($name, $value = null)
	{
		parent::setSetting($name, $value);

		$this->settings = $this->fieldSettings;
		return $this;
	}

	public function getSettingsAttribute()
	{
		return $this->fieldSettings = $this->attributes['settings'];
	}

	/**
	 * @return string
	 */
	protected function getSettingsProperty()
	{
		return 'fieldSettings';
	}

	/**
	 * @return float|int
	 */
	public function getLastPosition()
	{
		return DB::table($this->getTable())->where('ds_id', $this->ds_id)->max('position');
	}

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @param  string|null  $connection
	 * @return static
	 */
	public function newFromBuilder($attributes = [], $connection = null)
	{
		$model = $this->newInstance(['type' => array_get((array)$attributes, 'type')], true);
		$model->setRawAttributes((array) $attributes, true);
		$model->setConnection($connection ?: $this->connection);

		$model->fill(['settings' => $this->defaultSettings()]);

		return $model;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return static
	 */
	public function newInstance($attributes = [], $exists = false)
	{
		// This method just provides a convenient way for us to generate fresh model
		// instances of this current model. It is particularly useful during the
		// hydration of new objects via the Eloquent query builder instances.
		if (isset($attributes['type']) and !is_null($type = FieldManager::getFieldTypeBy('type', $attributes['type'])))
		{
			$class = $type->getClass();
			unset($attributes['type']);
			$model = new $class((array) $attributes);
		}
		else
		{
			$model = new static((array) $attributes);
		}

		$model->exists = $exists;

		return $model;
	}
}