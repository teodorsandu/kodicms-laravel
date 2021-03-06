<?php namespace KodiCMS\Pages\Http\Controllers\API;

use KodiCMS\Pages\Model\Page;
use KodiCMS\Pages\Repository\PageRepository;
use KodiCMS\Users\Model\UserMeta;
use KodiCMS\Pages\Model\PageSitemap;
use KodiCMS\Pages\Model\FrontendPage;
use KodiCMS\API\Http\Controllers\System\Controller as APIController;

class PageController extends APIController
{
	/**
	 * @param PageRepository $repository
	 */
	public function getChildren(PageRepository $repository)
	{
		$parentId = (int) $this->getRequiredParameter('parent_id');
		$level = (int) $this->getParameter('level');

		$this->setContent($this->_children($repository, $parentId, $level));
	}

	/**
	 * @param PageRepository $repository
	 * @param integer $parentId
	 * @param integer $level
	 * @return null|string
	 */
	protected function _children(PageRepository $repository, $parentId, $level)
	{
		$expandedRows = UserMeta::get('expanded_pages', []);

		$page = $repository->find($parentId);

		if (is_null($page))
		{
			return null;
		}

		$childrens = $repository->getChildrenByPageId($parentId);

		foreach ($childrens as $id => $child)
		{
			$childrens[$id]->hasChildren = $child->hasChildren();
			$childrens[$id]->isExpanded = in_array($child->id, $expandedRows);

			if ($childrens[$id]->isExpanded === true)
			{
				$childrens[$id]->childrenRows = $this->_children($repository, $child->id, $level + 1);
			}
		}

		return view('pages::pages.children', [
			'childrens' => $childrens,
			'level'     => $level + 1
		])->render();
	}

	/**
	 * @param PageRepository $repository
	 */
	public function getReorder(PageRepository $repository)
	{
		$pages = $repository->getSitemap(true)->asArray();

		$this->setContent(view('pages::pages.reorder', [
			'pages' => $pages
		]));
	}

	/**
	 * @param PageRepository $repository
	 */
	public function postReorder(PageRepository $repository)
	{
		$pages = $this->getRequiredParameter('pids', []);

		if (empty($pages)) return;

		$this->setContent($repository->reorder($pages));
	}

	/**
	 * @param PageRepository $repository
	 */
	public function postChangeStatus(PageRepository $repository)
	{
		$pageId = $this->getRequiredParameter('page_id');
		$value = $this->getRequiredParameter('value');

		$page = $repository->update($pageId, [
			'status' => $value
		]);

		$this->setContent($page->getStatus());
	}

	/**
	 * @param PageRepository $repository
	 */
	public function getSearch(PageRepository $repository)
	{
		$query = trim($this->getRequiredParameter('search'));

		$pages = $repository->searchByKeyword($query);
		$childrens = [];

		foreach ($pages as $page)
		{
			$page->isExpanded = false;
			$page->hasChildren = false;

			$childrens[] = $page;
		}

		$this->setContent(view('pages::pages.children', [
			'childrens' => $childrens,
			'level'     => 0
		]));
	}
}