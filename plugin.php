<?php

	class CustomFieldsPlugin extends CMSPlugin {

		protected $uri;
		protected $dir;
		protected $field_types;

		public function install($options) {
			// Do nothing
		}

		public function activate() {
			// Do nothing
		}

		public function deactivate() {
			// Do nothing
		}

		public function uninstall($options) {
			// Do nothing
		}

		public function load($uri, $dir) {
			global $site;
			$site->registerHook('cms.adminEditorAfterWidgets', 'CustomFieldsPlugin::handleEditorAfterWidgets');
			$site->registerHook('cms.adminEditorAfterTitle', 'CustomFieldsPlugin::handleEditorAfterTitle');
			$site->registerHook('cms.adminEditorAfterContent', 'CustomFieldsPlugin::handleEditorAfterContent');
			// $site->registerHook('core.includeScripts', 'CustomFieldsPlugin::handleIncludeScripts');
			$site->registerHook('cms.adminInit', 'CustomFieldsPlugin::handleAdminInit');
			// #
			// $site->registerScript('recaptcha-plugin', "{$uri}/plugin.js", true, ['jquery']);
			// $site->registerScript('recaptcha', 'https://www.google.com/recaptcha/api.js', true);
			// #
			$this->uri = $uri;
			$this->dir = $dir;
		}

		public function init() {
			global $site;
			$this->field_types = [];
			$this->registerFieldType('text', ['renderer' => 'CustomFieldsPlugin::renderText']);
			$this->registerFieldType('number', ['renderer' => 'CustomFieldsPlugin::renderNumber']);
			$this->registerFieldType('url', ['renderer' => 'CustomFieldsPlugin::renderURL']);
			$this->registerFieldType('email', ['renderer' => 'CustomFieldsPlugin::renderEmail']);
			$this->registerFieldType('textarea', ['renderer' => 'CustomFieldsPlugin::renderTextarea']);
			$this->registerFieldType('range', ['renderer' => 'CustomFieldsPlugin::renderRange']);
			$this->registerFieldType('select', ['renderer' => 'CustomFieldsPlugin::renderSelect']);
			$this->registerFieldType('checkgroup', ['renderer' => 'CustomFieldsPlugin::renderCheckGroup']);
			$this->registerFieldType('radiogroup', ['renderer' => 'CustomFieldsPlugin::renderRadioGroup']);
			$this->registerFieldType('truefalse', ['renderer' => 'CustomFieldsPlugin::renderTrueFalse']);
			$this->registerFieldType('image', ['renderer' => 'CustomFieldsPlugin::renderImage', 'processor' => 'CustomFieldsPlugin::processImage']);
			$this->registerFieldType('gallery', ['renderer' => 'CustomFieldsPlugin::renderGallery', 'processor' => 'CustomFieldsPlugin::processGallery']);
			$this->registerFieldType('file', ['renderer' => 'CustomFieldsPlugin::renderFile', 'processor' => 'CustomFieldsPlugin::processFile']);
			$this->registerFieldType('repeater', ['renderer' => 'CustomFieldsPlugin::renderRepeater', 'processor' => 'CustomFieldsPlugin::processRepeater']);
			$this->registerFieldType('content', ['renderer' => 'CustomFieldsPlugin::renderContent', 'processor' => 'CustomFieldsPlugin::processContent']);
			$this->registerFieldType('entity', ['renderer' => 'CustomFieldsPlugin::renderEntity', 'processor' => 'CustomFieldsPlugin::processEntity']);
			$this->registerFieldType('link', ['renderer' => 'CustomFieldsPlugin::renderLink', 'processor' => 'CustomFieldsPlugin::processLink']);
			$this->registerFieldType('tab', ['renderer' => 'CustomFieldsPlugin::renderTab']);
			$site->executeHook('customFields.init', $this);
		}

		public function getActions() {
			global $site;
			$ret = [];
			$ret['settings'] = ['label' => 'Settings', 'url' => $site->cms->admin->adminUrl('/custom-fields')];
			return $ret;
		}

		public function registerFieldType($name, $config) {
			$this->field_types[$name] = $config;
		}

		public function unregisterFieldType($name) {
			if ( isset( $this->field_types[$name] ) ) {
				unset( $this->field_types[$name] );
			}
		}

		public function getFieldType($name) {
			return get_item($this->field_types, $name);
		}

		public static function handleAdminInit($admin) {
			global $site;
			$admin->registerMenu('custom-fields', 'Custom fields', '/custom-fields', 'fa-flash', null, 91);
			$admin->registerMenu('custom-fields-index', 'Field groups', '/custom-fields', false, 'custom-fields');
			$admin->registerMenu('custom-fields-add', 'Add field group', '/custom-fields/new', false, 'custom-fields');
			//
			$admin->registerController('custom-fields', 'custom-fields', 'CustomFieldsPlugin::controllerCustomFields');
			//
			$site->registerHook('cms.adminHeaderHtml', 'CustomFieldsPlugin::handleAdminHeaderHtml');
			$site->registerHook('cms.adminFooterHtml', 'CustomFieldsPlugin::handleAdminFooterHtml');
			$site->registerHook('cms.adminAfterUpsert', 'CustomFieldsPlugin::handleAdminAfterUpsert');
			//
			$site->addBodyClass('has-custom-fields');
		}

		public static function handleAdminHeaderHtml() {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			if ( is_object($plugin) ) {
				echo '<link rel="stylesheet" href="'.$plugin->uri.'/assets/styles/custom-fields.css">';
			}
		}

		public static function handleAdminFooterHtml() {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			if ( is_object($plugin) ) {
				$partial_dir = "{$plugin->dir}/templates";
				$site->partial('gallery-item', [], $partial_dir);
				echo '<script type="text/javascript" src="'.$plugin->uri.'/assets/scripts/custom-fields.js"></script>';
			}
		}

		public static function handleAdminAfterUpsert($entity) {
			global $site;
			$request = $site->getRequest();
			$fields = $request->post('fields');
			$_fields = $request->post('_fields');
			if ($entity && $entity->id) {
				foreach ($fields as $key => $value) {
					$entity->updateMeta($key, is_array($value) && count($value) == 0 ? '' : $value);
				}
			}
			# Save fields shadow data (for decoding)
			$entity->updateMeta('_fields', $_fields);
		}

		public static function handleEditorAfterWidgets($obj) {
			return self::includeFieldsByPosition('side', $obj);
		}

		public static function handleEditorAfterTitle($obj) {
			return self::includeFieldsByPosition('high', $obj);
		}

		public static function handleEditorAfterContent($obj) {
			return self::includeFieldsByPosition('normal', $obj);
		}

		public static function includeFieldsByPosition($position, $obj) {
			$ret = false;
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			if ( is_object($plugin) ) {
				$field_groups = $plugin->getFieldGroups($position);
				$ret = $plugin->includeFieldGroup($field_groups, $obj);
			}
			return $ret;
		}

		public static function controllerCustomFields($action, $index, $params) {
			global $site;
			$request = $site->getRequest();
			$plugin = $site->cms->getPlugin('custom-fields');
			$ret = false;
			#
			$site->cms->admin->requireUser();
			#
			$entity = null;
			$templates_dir = "{$plugin->dir}/templates";
			switch ($action) {
				case 'index':
					switch ($request->type) {
						case 'get':
							$args = [];
							$args['conditions'] = "type = 'field_group' AND status != 'Trash'";
							$args['sort'] = 'asc';
							$args['by'] = 'id';
							$entities = Entities::all($args);
							//
							$data = [];
							$data['entities'] = $entities;
							$data['templates_dir'] = $templates_dir;
							$site->setPageTitle( $site->getPageTitle('Field Groups') );
							$site->partial('section-index', $data, $templates_dir);
							$ret = true;
						break;
					}
				break;
				case 'new':
					switch ($request->type) {
						case 'get':
							$site->setPageTitle( $site->getPageTitle('Add Field Group') );
							#
							$data = [];
							$data['entity'] = $entity;
							$data['templates_dir'] = $templates_dir;
							$site->partial('section-new', $data, $templates_dir);
							$ret = true;
						break;
						case 'post':
							$csrf = $request->post('csrf', '');
							if ( $site->cms->checkCsrfToken('admin.customfields', $csrf) ) {
								$fields = $request->post();
								$entity = self::upsertFieldGroup($entity, $fields);
								$site->redirectTo( $site->cms->admin->adminUrl("/custom-fields/edit/{$entity->id}?msg=MSG_FIELD_GROUP_SAVED") );
								$ret = true;
							} else {
								$site->errorMessage('ERR_INVALID_TOKEN');
							}
						break;
					}
				break;
				case 'edit':
					$entity = Entities::getById($index);
					switch ($request->type) {
						case 'get':
							$site->setPageTitle( $site->getPageTitle('Edit Field Group') );
							#
							$data = [];
							$data['entity'] = $entity;
							$data['templates_dir'] = $templates_dir;
							$site->partial('section-edit', $data, $templates_dir);
							$ret = true;
						break;
						case 'post':
							$csrf = $request->post('csrf', '');
							if ( $site->cms->checkCsrfToken('admin.customfields', $csrf) ) {
								$fields = $request->post();
								$entity = self::upsertFieldGroup($entity, $fields);
								$site->redirectTo( $site->cms->admin->adminUrl("/custom-fields/edit/{$entity->id}?msg=MSG_FIELD_GROUP_SAVED") );
								$ret = true;
							} else {
								$site->errorMessage('ERR_INVALID_TOKEN');
							}
						break;
					}
				break;
				case 'duplicate':
					$entity = Entities::getById($index);
					if ($entity) {
						switch ($request->type) {
							case 'get':
								$csrf = $request->get('csrf');
								if ( $site->cms->checkCsrfToken('admin.customfields', $csrf) ) {
									$entity->id = 0;
									$entity->save();
									$site->redirectTo( $site->cms->admin->adminUrl("/custom-fields?msg=MSG_FIELD_GROUP_DUPLICATED") );
									$ret = true;
								} else {
									$site->errorMessage('ERR_INVALID_TOKEN');
								}
							break;
						}
					}
				break;
				case 'delete':
					$entity = Entities::getById($index);
					if ($entity) {
						switch ($request->type) {
							case 'get':
								$site->setPageTitle( $site->getPageTitle('Delete Field Group') );
								#
								$data = [];
								$data['entity'] = $entity;
								$data['templates_dir'] = $templates_dir;
								$site->partial('section-delete', $data, $templates_dir);
								$ret = true;
							break;
							case 'post':
								$entity->status = 'Trash';
								$entity->save();
								$site->redirectTo( $site->cms->admin->adminUrl("/custom-fields?msg=MSG_FIELD_GROUP_DELETED") );
								$ret = true;
							break;
						}
					}
				break;
			}
			return $ret;
		}

		public static function renderDebug($config, $value = '') {
			echo "<p><em>Unknown field type {$config->name}, showing config:</em></p>";
			print_a($config);
			print_a($value ?: '--');
		}

		public static function renderText($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-text', $data, $partial_dir);
		}

		public static function renderNumber($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-number', $data, $partial_dir);
		}

		public static function renderURL($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-url', $data, $partial_dir);
		}

		public static function renderEmail($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-email', $data, $partial_dir);
		}

		public static function renderTextarea($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-textarea', $data, $partial_dir);
		}

		public static function renderRange($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-range', $data, $partial_dir);
		}

		public static function renderSelect($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-select', $data, $partial_dir);
		}

		public static function renderCheckGroup($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-checkgroup', $data, $partial_dir);
		}

		public static function renderRadioGroup($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-radiogroup', $data, $partial_dir);
		}

		public static function renderTrueFalse($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-truefalse', $data, $partial_dir);
		}

		public static function renderImage($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-image', $data, $partial_dir);
		}

		public static function renderGallery($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-gallery', $data, $partial_dir);
		}

		public static function renderFile($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-file', $data, $partial_dir);
		}

		public static function renderRepeater($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['plugin'] = $plugin;
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-repeater', $data, $partial_dir);
		}

		public static function renderContent($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['plugin'] = $plugin;
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-content', $data, $partial_dir);
		}

		public static function renderEntity($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['plugin'] = $plugin;
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-entity', $data, $partial_dir);
		}

		public static function renderLink($config, $value = '') {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$partial_dir = "{$plugin->dir}/templates";
			$data = [];
			$data['plugin'] = $plugin;
			$data['config'] = $config;
			$data['value'] = $value;
			$site->partial('field-link', $data, $partial_dir);
		}

		public static function renderTab($config, $value = '') {
			# Dummy, does nothing
		}

		public static function processImage($value, $name, $fields) {
			global $site;
			$ret = $site->cms->getImage($value, null, 'object');
			return $ret;
		}

		public static function processGallery($value, $name, $fields) {
			global $site;
			$ret = [];
			if ( is_array($value) ) {
				foreach ($value as $id) {
					$ret[] = $site->cms->getImage($id, null, 'object');
				}
			}
			return $ret;
		}

		public static function processFile($value, $name, $fields) {
			global $site;
			$ret = $site->cms->getAttachment($value, 'object');
			return $ret;
		}

		public static function processRepeater($value, $name, $fields) {
			global $site;
			$ret = [];
			$plugin = $site->cms->getPlugin('custom-fields');
			$subfields = get_item($fields, "_subfields-{$name}");
			# Process subfield values
			if ($subfields) {
				$subfields = @unserialize($subfields);
				if ($subfields) {
					foreach ($value as $key => $subfield) {
						$ret[$key] = [];
						if ($subfield) {
							$type = get_item($subfields, $key);
							$type = $plugin->getFieldType($type);
							$processor = $type ? get_item($type, 'processor') : null;
							if ( is_callable($processor) ) {
								foreach ($subfield as $val) {
									$ret[$key][] = call_user_func($processor, $val, $key, null);
								}
							} else {
								$ret[$key] = $subfield;
							}
						}
					}
				}
			}
			# Now format repeater values
			$values = [];
			if ($ret) {
				foreach ($ret as $name => $items) {
					for ($i = 0; $i < count($items); $i++) {
						if (! isset( $values["item_{$i}"] ) ) {
							$values["item_{$i}"] = [];
						}
						$values["item_{$i}"][$name] = $items[$i];
					}
				}
				$ret = array_values($values);
			}
			return $ret;
		}

		public static function processContent($value, $name, $fields) {
			global $site;
			$ret = $site->cms->filterText($value, false, 'text/markdown');
			return $ret;
		}

		public static function processEntity($value, $name, $fields) {
			$ret = Entities::getById($value);
			return $ret;
		}

		public static function processLink($value, $name, $fields) {
			$entity = Entities::getById($value);
			$ret = $entity ? $entity->getPermalink() : false;
			return $ret;
		}

		public static function getField($name, $id, $echo = false) {
			global $site;
			$plugin = $site->cms->getPlugin('custom-fields');
			$dbh = $site->getDatabase();
			$ret = false;
			#
			$value = null;
			$_fields = null;
			#
			if ( is_numeric($id) ) {
				$entity = Entities::getById($id);
				$value = $entity ? $entity->getMeta($name) : '';
				$_fields = $entity->getMeta('_fields');
			}
			if ($value) {
				$ret = $value;
				if ($_fields) {
					$field = get_item($_fields, $name);
					$type = $plugin->getFieldType($field);
					$processor = $type ? get_item($type, 'processor') : null;
					if ( is_callable($processor) ) {
						$ret = call_user_func($processor, $ret, $name, $_fields);
					}
				}
			}
			#
			if ($echo) {
				echo is_array($ret) ? print_r($ret, 1) : $ret;
			}
			return $ret;
		}

		protected function getFieldGroups($position) {
			global $site;
			$ret = [];
			#
			$args = [];
			$args['conditions'] = "type = 'field_group' AND status = 'Published'";
			$field_groups = Entities::all($args);
			#
			$entity_type = '';
			$taxonomy_type = '';
			$user_type = '';
			#
			$admin_controller = $site->cms->admin->getVar('controller');
			$admin_action = $site->cms->admin->getVar('action');
			$admin_index = $site->cms->admin->getVar('index');
			$admin_module = $site->cms->admin->getVar('module');
			$admin_type = $site->cms->admin->getVar('type');
			// print_a([$admin_module, $admin_type]);
			#
			switch ($admin_module) {
				case 'entity':
					$entity_type = $admin_type;
				break;
				case 'taxonomy':
					$taxonomy_type = $admin_type;
				break;
				case 'user':
					// $user_type =
				break;
			}
			#
			if ($field_groups) {
				foreach ($field_groups as $field_group) {
					$applies = false;
					$config = @json_decode($field_group->content);
					if ($config) {
						$config->name = $field_group->title;
						if ($config->location) {
							foreach ($config->location as $rule) {
								switch ($rule->param) {
									case 'entity_type':
										$applies = $admin_module == 'entity' && ($rule->operator == '==' ? $entity_type === $rule->value : $entity_type !== $rule->value);
									break;
									case 'user_type':
										// $applies = ($rule->operator == '==' ? $site->cms->user->type == $rule->value : $site->cms->user->type != $rule->value);
									break;
									case 'entity':
										$applies = $admin_module == 'entity' && ($rule->operator == '==' ? $admin_index === $rule->value : $admin_index !== $rule->value);
									break;
									case 'taxonomy':
										$applies = $admin_module == 'taxonomy' && ($rule->operator == '==' ? $admin_index === $rule->value : $admin_index !== $rule->value);
									break;
									case 'term':
										$applies = $admin_module == 'term' && ($rule->operator == '==' ? $admin_index === $rule->value : $admin_index !== $rule->value);
									break;
									print_a($admin_module);
								}
								if ($applies) break
							}
						}
						if ($applies && $config->settings->position == $position) {
							$ret[] = $config;
						}
					}
				}
			}
			return $ret;
		}

		protected function includeFieldGroup($field_groups, $obj) {
			global $site;
			$tab_open = false;
			if ($field_groups) {
				foreach ($field_groups as $field_group) {
					if ($field_group->fields) {
						#
						$style = get_item($field_group->settings, 'style', 'standard');
						#
						if ($style == 'standard') {
							echo '<div class="metabox"><div class="metabox-header">'.$site->cms->sanitizeText($field_group->name).'</div><div class="metabox-body">';
						}
						#
						foreach ($field_group->fields as $field) {
							$type = $this->getFieldType($field->type);
							$renderer = get_item($type, 'renderer');
							$value = $obj ? $obj->getMeta($field->name) : '';
							$field->slug = $field->name;
							$field->field = $field->name;
							#
							if ($field->type == 'tab') {
								if ($tab_open) {
									# Close previous tab
									echo "</div>";
								} else {
									# Open root tabs
									echo '<div class="tabs tabs-custom-fields">';
								}
								# Open a new tab
								echo '<div class="tab" data-label="'.$site->cms->sanitizeText($field->label).'">';
								# Set flag
								$tab_open = true;
							}
							#
							if ($field->field) {
								printf('<input type="hidden" name="_fields[%s]" value="%s">', $field->field, $field->type);
							}
							#
							if ($type && is_callable($renderer)) {
								call_user_func($renderer, $field, $value);
							} else {
								CustomFieldsPlugin::renderDebug($field, $value);
							}
						}
						if ($tab_open) {
							# Close last tab
							echo "</div>";
							# Close root tabs
							echo "</div>";
						}
						#
						if ($style == 'standard') {
							echo '</div></div>';
						}
						#
					}
				}
			}
		}

		protected function upsertFieldGroup($entity, $fields) {
			global $site;
			if (!$entity) {
				$entity = new Entity();
				$entity->type = 'field_group';
			}
			#
			$slug = $site->toAscii($entity->title);
			$entity->title = get_item($fields, 'title');
			$entity->slug = "field_group_{$slug}";
			$entity->status = get_item($fields, 'status', 'Published');
			$entity->mime_type = get_item($fields, 'mime_type', 'application/json');
			$entity->published = get_item($fields, 'published', date('Y-m-d H:i:s'));
			$entity->parent = get_item($fields, 'parent', 0);
			$entity->content = get_item($fields, 'content');
			$entity->author = get_item($fields, 'author', $site->cms->user->id);
			$entity->excerpt = get_item($fields, 'excerpt');
			#
			$entity->save();
			#
			return $entity;
		}

		// public function getFieldObject($name, $id_entity)

	}

?>