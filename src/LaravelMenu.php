<?php


namespace Aigletter\LaravelMenu;


use Aigletter\LaravelMenu\Contracts\MenuItemRepositoryInterface;
use Aigletter\LaravelMenu\Contracts\MenuRepositoryInterface;
use Aigletter\Menu\Builder\MenuBuilder;
use Aigletter\Menu\Interfaces\MenuInterface;
use Aigletter\Menu\MenuService;
use Aigletter\Menu\Render\MenuHtmlRenderer;
use Aigletter\Menu\Render\MenuJsonRenderer;
use Aigletter\Menu\Render\TemplateRenderer;

class LaravelMenu extends MenuService
{
    protected $menuRepository;

    protected $menuItemRepository;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        MenuItemRepositoryInterface $menuItemRepository,
        MenuBuilder $builder = null
    ) {
        parent::__construct($builder);

        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
    }

    public function getRepositoryMenu($name): MenuInterface
    {
        if ($menu = parent::getMenu($name)) {
            return $menu;
        }

        $menuModel = $this->menuRepository->findByName($name);

        if (!$menuModel) {
            throw new \Exception('Menu with name ' . $name . ' not found in repository');
        }

        $itemModels = $this->menuItemRepository->getByMenuId($menuModel['id']);
        $menu = $this->makeMenu($name, function(MenuBuilder $builder) use ($itemModels) {
            $this->addItems($builder, $itemModels);
        });


        return $menu;
    }

    protected function addItems(MenuBuilder $builder, $items)
    {
        foreach ($items as $item) {
            $builder->addItem($item['id'], $item['title'], $item['url']);

            if (isset($item['items']) && count($item['items'])) {
                $builder->addSubmenu('submenu-' . $item->id, function(MenuBuilder $builder) use ($item) {
                    $this->addItems($builder, $item['items']);
                });
            }
        }
    }

    protected function addHtmlRendererConfig($config, $callback)
    {
        if (empty($config['tag'])) {
            throw new \Exception('Tag is required');
        }

        $tag = $config['tag'];
        unset($config['tag']);
        $attributes = $config['attributes'] ?? [];

        $callback($tag, $attributes);
    }

    public function renderHtmlRepositoryMenu($name, $options = [])
    {
        $menu = $this->getRepositoryMenu($name);

        $renderer = new MenuHtmlRenderer();

        if (isset($options['menuWrapper'])) {
            $this->addHtmlRendererConfig($options['menuWrapper'], function($tag, $attributes) use ($renderer) {
                $renderer->setMenuWrapperConfig($tag, $attributes);
            });
        }

        if (isset($options['itemWrapper'])) {
            $this->addHtmlRendererConfig($options['itemWrapper'], function($tag, $attributes) use ($renderer) {
                $renderer->setItemElementConfig($tag, $attributes);
            });
        }

        if (isset($options['submenuWrapper'])) {
            $this->addHtmlRendererConfig($options['submenuWrapper'], function($tag, $attributes) use ($renderer) {
                $renderer->setSubmenuWrapperConfig($tag, $attributes);
            });
        }

        if (isset($options['linkAttributes'])) {
            $renderer->setLinkElementAttributes($options['linkAttributes']);
        }

        return $renderer->render($menu);
    }

    public function renderJsonRepositoryMenu($name)
    {
        $menu = $this->getRepositoryMenu($name);

        $renderer = new MenuJsonRenderer();

        return $renderer->render($menu);
    }

    public function renderTemplateRepositoryMenu($name, $template)
    {
        $menu = $this->getRepositoryMenu($name);

        $renderer = new TemplateRenderer($template);

        $renderer->render($menu);
    }
}