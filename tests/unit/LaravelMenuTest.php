<?php


namespace Aigletter\LaravelMenu\Tests\Units;


use Aigletter\LaravelMenu\LaravelMenu;
use Aigletter\LaravelMenu\LaravelMenuServiceProvider;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use Spatie\Menu\Laravel\MenuServiceProvider;

class LaravelMenuTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MenuServiceProvider::class,
            LaravelMenuServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Menu' => \Spatie\Menu\Laravel\Facades\Menu::class,
            'LaravelMenu' => LaravelMenu::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ]);

        $app['config']->set('laravel-menu.settings.default', [
            'auto_activate' => true,
            'activate_parents' => true,
            'active_class' => 'active',
            'restful' => false,
            'cascade_data' => true,
            'rest_base' => '',
            'active_element' => 'item',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $dir = realpath(__DIR__ . '/../../database/migrations');
        $this->loadMigrationsFrom($dir);

        $id = DB::table('menus')->insertGetId([
            'name' => 'test',
            'title' => 'Test Title'
        ]);
        DB::table('menu_items')->insert([
            'menu_id' => $id,
            'title' => 'Test Title One',
            'url' => 'test/one',
        ]);
        $parentId = DB::table('menu_items')->insertGetId([
            'menu_id' => $id,
            'title' => 'Test Title Two',
            'url' => 'test/two',
        ]);

        DB::table('menu_items')->insert([
            'menu_id' => $id,
            'title' => 'Test Children',
            'url' => 'test/children',
            'parent_id' => $parentId,
            'level' => 2
        ]);
    }

    public function tearDown(): void
    {
        $this->artisan('migrate:rollback', ['--database' => 'mysql'])->run();

        parent::tearDown();
    }

    public function testMakeFromModel()
    {
        /** @var LaravelMenu $service */
        $service = $this->app->get(LaravelMenu::class);
        $menu = $service->getRepositoryMenu('test');

        $this->assertCount(2, $menu->getItems());
    }

    public function testRenderHtmlRepositoryMenu()
    {
        /** @var LaravelMenu $service */
        $service = $this->app->get(LaravelMenu::class);
        $output = $this->clearHtml($service->renderHtmlRepositoryMenu('test', [
            'menuWrapper' => ['tag' => 'div', 'attributes' => ['class' => 'nav']],
            'itemWrapper' => ['tag' => 'span', 'attributes' => ['class' => 'nav-item']],
            'linkAttributes' => ['class' => 'nav-link']
        ]));
        $expected = $this->clearHtml('
            <div id="test" class="nav">
                <span class="nav-item" id="1">
                    <a href="test/one" class="nav-link">Test Title One</a>
                </span>
                <span class="nav-item" id="2">
                    <a href="test/two" class="nav-link">Test Title Two</a>
                    <div class="nav">
                        <span class="nav-item" id="3">
                            <a href="test/children" class="nav-link">Test Children</a>
                        </span>
                    </div>
                </span>
            </div>
        ');

        $this->assertEquals($expected, $output);
    }

    public function testRenderJsonRepositoryMenu()
    {
        /** @var LaravelMenu $service */
        $service = $this->app->get(LaravelMenu::class);
        $output = $service->renderJsonRepositoryMenu('test');
        $expected = $this->clearJson('{
            "name":"test",
            "items":[
                {
                    "id":"1",
                    "title":"Test Title One",
                    "url":"test/one"
                },
                {
                    "id":"2",
                    "title":"Test Title Two",
                    "url":"test/two",
                    "submenu":{
                        "name":"submenu-2",
                        "items":[
                            {
                                "id":"3",
                                "title":"Test Children",
                                "url":"test/children"
                            }
                        ]
                    }
                }
            ]
        }');

        $this->assertEquals($expected, $output);
    }

    /*public function testRenderTemplateRepositoryMenu()
    {
        $service = $this->app->get(LaravelMenu::class);

        $service->getRepositoryMenu();
    }*/

    protected function clearHtml($html)
    {
        return trim(preg_replace(["/\s{2,}/", "/(\>)[\s\n]*(\<)/m"], [' ', '$1$2'], $html));
    }

    protected function clearJson($json)
    {
        return json_encode(json_decode($json));
    }
}