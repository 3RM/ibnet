<?php
namespace console\controllers;

use common\models\blog\WpPosts;
use common\models\Mail;
use common\models\User;
use fedemotta\cronjob\models\CronJob; use common\models\Utility;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class BlogController extends Controller
{
    
    /**
     * Send out weekly blog notice
     * Cron job can be found at ~/crontab -e
     * @param string $from
     * @param string $to
     * @return int exit code
     */
    public function actionInit($from, $to) {
        $dates  = CronJob::getDateRange($from, $to);
        $command = CronJob::run($this->id, $this->action->id, 0, CronJob::countDateRange($dates));
        if ($command === false) {
            return Controller::EXIT_CODE_ERROR;
        } else {
            foreach ($dates as $date) {
                if ($posts = WpPosts::getWeeksPosts()) {
                    $users = User::getAllSubscribedBlog();
             
                    foreach ($users as $user) {

                        Yii::$app
                            ->mailer
                            ->compose(
                                ['html' => 'blog/weekly-blog-html', 'text' => 'blog/weekly-blog-text'], 
                                [
                                    'title' => 'This Week\'s Blog Posts', 
                                    'message' => 'Read the latest articles from the IBNet Blog: ', 
                                    'posts' => $posts,
                                ])
                            ->setFrom([\Yii::$app->params['email.blogDigest']])
                            ->setTo($user->email)
                            ->setSubject('IBNet Blog')
                            ->send();
                    }
                }
            }
            $command->finish();
            return Controller::EXIT_CODE_NORMAL;
        }
    }

    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionIndex() {
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));                     // php yii blog/index
    }

}
