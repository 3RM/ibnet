<?php
namespace frontend\controllers;

use common\models\profile\Profile;
use yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use xj\sitemap\models\Url;
use xj\sitemap\models\BaiduUrl;
use xj\sitemap\actions\SitemapUrlsetAction;
use xj\sitemap\actions\SitemapIndexAction;

/**
 * Site controller
 */
class SitemapController extends Controller
{

    public function actions()
    {

        return [
            //Google Sitemap By ActiveRecord
            'sitemap-google-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-google-urlset'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where('status=' . Profile::STATUS_ACTIVE),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 50000, //per page 1 record
                    ]]),
            ],
            'sitemap-google-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where('status=' . Profile::STATUS_ACTIVE),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 50000,
                    ]]),
                'remap' => function ($model) {
                        /* @var $model Profile */
                        $url = Url::create([
                            'loc' => \yii\helpers\Url::to(['/profile/' . ProfileController::$profilePageArray[$model->type], 'id' => $model->id, 'urlLoc' => $model->url_loc, 'name' => $model->url_name], true),
                            'lastmod' => $model->last_modified,
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.7',
                        ]);
                        return $url;
                },
            ],

            //Baidu Mobile Sitemap By ActiveRecord
            'sitemap-baidumobile-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-baidumobile-urlset'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where('status=' . Profile::STATUS_ACTIVE),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 50000, //per page 1 record
                    ]]),
            ],
            'sitemap-baidumobile-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'urlClass' => BaiduUrl::className(), //for Baidu
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where('status=' . Profile::STATUS_ACTIVE),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 50000,
                    ]]),
                'remap' => function ($model) {
                    /* @var $model Profile */
                    //return Array will auto using $urlClass::create()
                    return [
                        'loc' => \yii\helpers\Url::to(['/profile/' . ProfileController::$profilePageArray[$model->type], 'id' => $model->id, 'urlLoc' => $model->url_loc, 'name' => $model->url_name], true),
                        'lastmod' => $model->last_modified,
                        'changefreq' => Url::CHANGEFREQ_MONTHLY,
                        'priority' => '0.7',
                        'baiduType' => BaiduUrl::BAIDU_TYPE_MOBILE, // BaiduUrl::BAIDU_TYPE_ADAP | BaiduUrl::BAIDU_TYPE_HTMLADAP
                    ];
                },
            ],
            //FOR DIRECT DATA
            'sitemap-direct-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-direct'],
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => [
                        1, 2, 3, 4, 5 //only need number// p=1 | p=2 | p=3 | p=4
                    ],
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 100,
                    ]
                ]),
            ],
            'sitemap-direct-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => [
                        [
                            'loc' => 'https://ibnet.org/about',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.6',
                        ],
                        [
                            'loc' => 'https://ibnet.org/contact',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.6',
                        ],
                        [
                            'loc' => 'https://ibnet.org/privacy',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.3',
                        ],
                        [
                            'loc' => 'https://ibnet.org/terms',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.3',
                        ],
                        [
                            'loc' => 'https://ibnet.org/beliefs',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.8',
                        ],
                    ],
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 100,
                    ]
                ]),
                'remap' => function ($model) {
                    /* @var $model array */
                    return Url::create()->setAttributes($model);
                },
            ],

        ];
    }
}
