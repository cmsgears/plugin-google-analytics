<?php
// CMG Imports
use cmsgears\core\common\config\CoreGlobal;

use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\User;
use cmsgears\core\common\models\resources\Form;
use cmsgears\core\common\models\resources\FormField;

use cmsgears\core\common\utilities\DateUtil;

class m170601_072419_google_analytics extends \yii\db\Migration {

	// Public Variables

	// Private Variables

	private $prefix;

	private $site;
	private $master;

	private $uploadsDir;
	private $uploadsUrl;

	public function init() {

		// Table prefix
		$this->prefix	= Yii::$app->migration->cmgPrefix;

		$this->site		= Site::findBySlug( CoreGlobal::SITE_MAIN );
		$this->master	= User::findByUsername( Yii::$app->migration->getSiteMaster() );

		$this->uploadsDir	= Yii::$app->migration->getUploadsDir();
		$this->uploadsUrl	= Yii::$app->migration->getUploadsUrl();

		Yii::$app->core->setSite( $this->site );
	}

	public function up() {

		// Create various config
		$this->insertFileConfig();

		// Init default config
		$this->insertDefaultConfig();
	}

	private function insertFileConfig() {

		$this->insert( $this->prefix . 'core_form', [
				'siteId' => $this->site->id,
				'createdBy' => $this->master->id, 'modifiedBy' => $this->master->id,
				'name' => 'Config Google Analytics', 'slug' => 'config-google-analytics',
				'type' => CoreGlobal::TYPE_SYSTEM,
				'description' => 'Google analytics configuration form.',
				'successMessage' => 'All configurations saved successfully.',
				'captcha' => false,
				'visibility' => Form::VISIBILITY_PROTECTED,
				'active' => true, 'userMail' => false,'adminMail' => false,
				'createdAt' => DateUtil::getDateTime(),
				'modifiedAt' => DateUtil::getDateTime()
		]);

		$config	= Form::findBySlug( 'config-google-analytics', CoreGlobal::TYPE_SYSTEM );

		$columns = [ 'formId', 'name', 'label', 'type', 'compress', 'validators', 'order', 'icon', 'htmlOptions' ];

		$fields	= [
			[ $config->id, 'global', 'Global', FormField::TYPE_TOGGLE, false, 'required', 0, NULL, '{"title":"Global"}' ],
			[ $config->id, 'global_code', 'Global Code', FormField::TYPE_TEXT, false, 'required', 0, NULL, '{"title":"Global Code","placeholder":"Global Code"}' ]
		];

		$this->batchInsert( $this->prefix . 'core_form_field', $columns, $fields );
	}

	private function insertDefaultConfig() {

		$columns = [ 'modelId', 'name', 'label', 'type', 'valueType', 'value' ];

		$metas	= [
			[ $this->site->id, 'global', 'Global', 'google-analytics', 'flag', '1' ],
			[ $this->site->id, 'global_code', 'Global Code', 'google-analytics', 'text', NULL ]
		];

		$this->batchInsert( $this->prefix . 'core_site_meta', $columns, $metas );
	}

	public function down() {

		echo "m170601_072419_google_analytics will be deleted with m160621_014408_core.\n";

		return true;
	}
}
