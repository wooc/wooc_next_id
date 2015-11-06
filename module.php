<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2015 £ukasz Wileñski.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
namespace Wooc\WebtreesAddon\WoocNextIdModule;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Tree;

class WoocNextIdModule extends AbstractModule implements ModuleConfigInterface {

	public function __construct() {
		parent::__construct('wooc_next_id');
	}

	// Extend Module
	public function getTitle() {
		return I18N::translate('Wooc Next ID Changer');
	}

	// Extend Module
	public function getDescription() {
		return I18N::translate('Allows you to easily change of the next ID for each record type.');
	}

	// Extend Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			$this->config();
			break;
		case 'admin_update':
			if (Filter::checkCsrf()) {
				$controller = new PageController;
				$controller->restrictAccess(Auth::isAdmin());
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='FAM' AND gedcom_id=?")->execute(array(Filter::post('NEW_FAM'), Filter::post('gedcom_id')));
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='INDI' AND gedcom_id=?")->execute(array(Filter::post('NEW_INDI'), Filter::post('gedcom_id')));
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='NOTE' AND gedcom_id=?")->execute(array(Filter::post('NEW_NOTE'), Filter::post('gedcom_id')));
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='OBJE' AND gedcom_id=?")->execute(array(Filter::post('NEW_OBJE'), Filter::post('gedcom_id')));
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='REPO' AND gedcom_id=?")->execute(array(Filter::post('NEW_REPO'), Filter::post('gedcom_id')));
				Database::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type='SOUR' AND gedcom_id=?")->execute(array(Filter::post('NEW_SOUR'), Filter::post('gedcom_id')));
			}
			$this->config();
			exit;
		}
	}

	// Implement Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}

	private function config() {
		global $WT_TREE;
		$controller=new PageController;
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Next ID changer'))
			->pageHeader();

		$gedcom_id = Filter::post('gedcom_id', null, $WT_TREE->getTreeId());
		$rows = Database::prepare("SELECT record_type, next_id FROM `##next_id` WHERE gedcom_id=?")
			->execute(array($gedcom_id))
			->fetchAll();
		$list = array();
		foreach ($rows as $row) {
			$list[$row->record_type] = $row->next_id;
		}
		static $type_to_preference = array(
			'INDI' => 'GEDCOM_ID_PREFIX',
			'FAM'  => 'FAM_ID_PREFIX',
			'OBJE' => 'MEDIA_ID_PREFIX',
			'NOTE' => 'NOTE_ID_PREFIX',
			'SOUR' => 'SOURCE_ID_PREFIX',
			'REPO' => 'REPO_ID_PREFIX',
		);
		?>
		<style>
			.text-left-not-xs, .text-left-not-sm, .text-left-not-md, .text-left-not-lg {
				text-align: left;
			}
			.text-center-not-xs, .text-center-not-sm, .text-center-not-md, .text-center-not-lg {
				text-align: center;
			}
			.text-right-not-xs, .text-right-not-sm, .text-right-not-md, .text-right-not-lg {
				text-align: right;
			}
			.text-justify-not-xs, .text-justify-not-sm, .text-justify-not-md, .text-justify-not-lg {
				text-align: justify;
			}

			@media (max-width: 767px) {
				.text-left-not-xs, .text-center-not-xs, .text-right-not-xs, .text-justify-not-xs {
					text-align: inherit;
				}
				.text-left-xs {
					text-align: left;
				}
				.text-center-xs {
					text-align: center;
				}
				.text-right-xs {
					text-align: right;
				}
				.text-justify-xs {
					text-align: justify;
				}
			}
			@media (min-width: 768px) and (max-width: 991px) {
				.text-left-not-sm, .text-center-not-sm, .text-right-not-sm, .text-justify-not-sm {
					text-align: inherit;
				}
				.text-left-sm {
					text-align: left;
				}
				.text-center-sm {
					text-align: center;
				}
				.text-right-sm {
					text-align: right;
				}
				.text-justify-sm {
					text-align: justify;
				}
			}
			@media (min-width: 992px) and (max-width: 1199px) {
				.text-left-not-md, .text-center-not-md, .text-right-not-md, .text-justify-not-md {
					text-align: inherit;
				}
				.text-left-md {
					text-align: left;
				}
				.text-center-md {
					text-align: center;
				}
				.text-right-md {
					text-align: right;
				}
				.text-justify-md {
					text-align: justify;
				}
			}
			@media (min-width: 1200px) {
				.text-left-not-lg, .text-center-not-lg, .text-right-not-lg, .text-justify-not-lg {
					text-align: inherit;
				}
				.text-left-lg {
					text-align: left;
				}
				.text-center-lg {
					text-align: center;
				}
				.text-right-lg {
					text-align: right;
				}
				.text-justify-lg {
					text-align: justify;
				}
			}
		</style>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<div class="row">
			<div class="col-sm-4 col-xs-12">
				<form class="form">
					<label for="ged" class="sr-only">
						<?php echo I18N::translate('Family tree'); ?>
					</label>
					<input type="hidden" name="mod" value="<?php echo  $this->getName(); ?>">
					<input type="hidden" name="mod_action" value="admin_config">
					<div class="col-sm-9 col-xs-9" style="padding:0;">
						<?php echo FunctionsEdit::selectEditControl('ged', Tree::getNameList(), null, $WT_TREE->getName(), 'class="form-control"'); ?>
					</div>
					<div class="col-sm-3" style="padding:1;">
						<input type="submit" class="btn btn-primary" value="<?php echo I18N::translate('show'); ?>">
					</div>
				</form>
			</div>
			<div class="col-sm-12 text-right text-left-xs col-xs-12">		
				<?php // TODO: Move to internal item/page
				if (file_exists(WT_MODULES_DIR . $this->getName() . '/readme.html')) { ?>
					<a href="<?php echo WT_MODULES_DIR . $this->getName(); ?>/readme.html" class="btn btn-info">
						<i class="fa fa-newspaper-o"></i>
						<?php echo I18N::translate('ReadMe'); ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<form method="post" name="configform" action="module.php?mod=<?php echo  $this->getName(); ?>&amp;mod_action=admin_update" class="form">
			<?php echo Filter::getCsrf(); ?>
			<input type="hidden" name="gedcom_id" value="<?php echo $gedcom_id; ?>">
				<?php
				foreach ($list as $record_type=>$next_id) {
					echo '<div class="form-group">
						<label class="control-label col-sm-3" for="NEW_', $record_type, '">', GedcomTag::getLabel($record_type), '</label>';
						if (array_key_exists($record_type, $type_to_preference)) {
							$prefix = $WT_TREE->getPreference($type_to_preference[$record_type]);
						} else {
							// Use the first non-underscore character
							$prefix = substr(trim($record_type, '_'), 0, 1);
						}
						echo '<div class="input-group col-sm-9"><span class="input-group-addon" style="min-width:36px;">', $prefix, '</span>
						<input
							class="form-control"
							id="NEW_', $record_type, '"
							size="10"
							width="20"
							name="NEW_', $record_type, '"
							type="number"
							style="max-width:340px;"
							value="', Filter::escapeHtml($next_id), '"
							>
						</div>
						<span class="help-block col-sm-9 col-sm-offset-3 small text-muted">',
						I18N::translate('The next saved record will has this or the next available ID number'), 
						'</span>
					</div>';
				}
				?>
				<div class="row col-sm-9 col-sm-offset-3">
					<button class="btn btn-primary" type="submit">
						<i class="fa fa-check"></i>
						<?php echo I18N::translate('save'); ?>
					</button>
					<button class="btn btn-primary" type="reset" onclick="window.location='<?php echo $this->getConfigLink(); ?>';">
						<i class="fa fa-recycle"></i>
						<?php echo I18N::translate('cancel'); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
	}
}

return new WoocNextIdModule;