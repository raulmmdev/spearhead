<?php

use App\Model\Entity\Site;
use App\Model\Entity\SiteCategory;
use Illuminate\Database\Seeder;

/**
 * SiteCategorySeeder
 */
class SiteCategorySeeder extends Seeder
{
    /**
     * Path where JSON file will be placed
     *
     * @var string
     */
    const JSON = 'seeds/json/categories/vinq.json';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $site = Site::find(Site::pluck('id')[0]);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Disable current tree (if any)

        SiteCategory::where('site_id', $site->id)->update(['status' => SiteCategory::STATUS_DISABLED]);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Load new tree

        $tree = $this->loadJson();

        if (is_array($tree) && count($tree)) {
            foreach ($tree as $category) {
                $this->processEntry($site, $category, $parentId = null);
            }
        }
    }

    /**
     * Loads the categories.json
     *
     * @access private
     * @return array
     */
    private function loadJson() : array
    {
        return json_decode(file_get_contents(database_path(self::JSON)), true);
    }

    /**
     * Recursively process each JSON entry
     *
     * @access private
     * @param  Site $site
     * @param  array $entry
     * @param  int $parentId
     * @return void
     */
    private function processEntry(Site $site, array $entry, $parentId) : void
    {
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Exist current thee?

        $category = SiteCategory::where('site_id', '=', $site->id)->where('source_id', '=', $entry['id'])->first();

        if ($category !== null) {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Enable category/child if proceed

            $category->title = json_encode($entry['title']);
            $category->cashback = $entry['cashback'] ?? config('qwindo.cashback.category');
            $category->status = SiteCategory::STATUS_ENABLED;
            $category->save();
        } else {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Create the parent category

            $category = new SiteCategory();
            $category->source_id = $entry['id'];
            $category->parent_id = $parentId;
            $category->title = json_encode($entry['title']);
            $category->cashback = $entry['cashback'] ?? config('qwindo.cashback.category');
            $category->status = $entry['status'] ?? SiteCategory::STATUS_ENABLED;
            $category->site()->associate($site);
            $category->save();
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Has children?

        if (isset($entry['children']) && is_array($entry['children']) && count($entry['children'])) {
            foreach ($entry['children'] as $child) {
                $this->processEntry($site, $child, $category->id);
            }
        }
    }
}
