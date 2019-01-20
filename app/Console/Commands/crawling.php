<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;

class crawling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:crawling {keyword} {searchCount} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'スクレイピングするコマンドです';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keyword = $this->argument("keyword");
        $searchCount= $this->argument("searchCount");

        //インスタンス生成
        $client = new Client();

        //検索アドレスと件数の指定
        $url = 'http://www.google.co.jp/search?num='.$searchCount.'&ie=UTF-8&q=' . urlencode($keyword);

        //取得とDOM構築
        $crawler = $client->request('GET',$url);

        $i = 0;
        $resultList = [];

        $crawler->filter('.g')->each(function($element) use(&$i,&$resultList){

            if($i==10){
                return;
            }

            $result = [];
            $title = '';
            $link = '';
            $description = '';

            if(!empty($element->filter('h3.r'))){
                $title = $element->filter('h3.r')->text();
            }

            if(!empty($element->attr('href'))){
                $link = $element->attr('href');
            }

            // if it is not a direct link but url reference found inside it, then extract
            if (!preg_match('/^https?/', $link) && preg_match('/q=(.+)&amp;sa=/U', $link, $matches) && preg_match('/^https?/', $matches[1])) {
                $link = $matches[1];
            } else if (!preg_match('/^https?/', $link)) { // skip if it is not a valid link
//                continue;
            }

            if(!empty($element->filter('span.st'))){
                $description = $element->filter('span.st')->text();
            }

            $result['title'] = $title;
            $result['url'] = urldecode($link);
            $result['description'] = $description;
            $result['rank'] = $i + 1;

            $resultList[] = $result;
            $i++;
        });


        //インスタンス生成
        $client = new Client();

        //検索アドレスと件数の指定
        $url = 'https://script.google.com/macros/s/AKfycbzwHcZCHlu78W1slVwBS2_yX7ktEzriRLy38WnfkTQ/dev?keyword=aa&rank=1&genre=脱毛&site_name=awww&domain=test.com&url=test.jp&project_1=あああ';

        //取得とDOM構築
        $a = $client->request('GET',$url);

        var_dump($a);
        var_dump($resultList);

    }
}
