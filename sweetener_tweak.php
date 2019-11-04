<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.sweetener
 * @copyright   Copyright (C) 2019 Artem Vasilev. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die;

/**
 * Sweetener plugin.
 *
 * @package   sweetener
 * @since     1.0.0
 */
class plgJshoppingAdminSweetener_Tweak extends CMSPlugin
{
    /**
     * @var    CMSApplication
     * @since  1.0.0
     */
    protected $app;


    /**
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;


    /**
     * @param $subject
     * @param $config
     * @since   1.0.0
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * @param $product
     * @param $related_products
     * @param $lists
     * @param $listfreeattributes
     * @param $tax_value
     * @since   1.0.0
     */
    public function onBeforeDisplayEditProduct(&$product, &$related_products, &$lists, &$listfreeattributes, &$tax_value)
    {
        HTMLHelper::_('formbehavior.chosen', '#form_groupid', null, array('disable_search_threshold' => 0));
        HTMLHelper::_('formbehavior.chosen', 'select');

        $jshopConfig = JSFactory::getConfig();

        $product_id = $product->product_id;
        $category_id = $this->app->input->getInt('category_id');

        if ($product_id) {
            $categories_select = $product->product_categories;
            $categories_select_list = array();
            foreach ($categories_select as $v) {
                $categories_select_list[] = $v->category_id;
            }
        } else {
            $categories_select = null;
            if ($category_id) {
                $categories_select = $category_id;
            }
            $categories_select_list = array();
        }

        $categories = buildTreeCategory(0, 1, 0);

        $category_select_onclick = "";
        if ($jshopConfig->admin_show_product_extra_field) {
            $category_select_onclick = 'onclick="reloadProductExtraField(\'' . $product_id . '\')"';
        }

        $lists['categories'] = JHTML::_('select.genericlist', $categories, 'category_id[]', 'class="chzn-done" size="10" multiple = "multiple" ' . $category_select_onclick, 'category_id', 'name', $categories_select);
    }

    /**
     * @param $view
     * @since 1.0
     */
    public function onBeforeEditCategories(&$view)
    {
        HTMLHelper::_('formbehavior.chosen', '#form_groupid', null, array('disable_search_threshold' => 0));
        HTMLHelper::_('formbehavior.chosen', 'select');

        $category = $view->category;
        $lists = $view->lists;

        if ($category->category_id) {
            $parentid = $category->category_parent_id;
        } else {
            $parentid = $this->app->input->getInt("catid");
        }

        $categories = JshopHelpersSelectOptions::getCategories(_JSHOP_TOP_LEVEL);

        $lists['treecategories'] = JHTML::_('select.genericlist', $categories, 'category_parent_id', 'class="chzn-done" onchange = "changeCategory()"', 'category_id', 'name', $parentid);

        $view->assign('lists', $lists);
    }
}
