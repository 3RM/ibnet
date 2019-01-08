<?php

namespace common\models\blog;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wp_posts" in dbblog.
 *
 * @property string $id
 * @property string $role
 */
class WpPosts extends Model
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wp_posts';
    }

    public static function getDb() {
	    return Yii::$app->dbblog;
	}

	/**
     * Get $n posts from Wordpress db
     * 
     * @param mixed $n
     * @return array
     */
	public function getPosts($n=6) 
	{
		$query = (new \yii\db\Query());
		$query->select(' 
			    post.id AS post_id,
			    post.post_author AS post_author,
			    post.post_date AS post_date,
			    post.post_title, 
			    post.post_content AS post_content,
			    post.guid AS post_url,
			    user.display_name AS author_name, 
			    image_detail.id AS image_id, 
			    image_detail.guid AS image_url');
		$query->from(['post' => 'wp_posts']);
		$query->join('LEFT JOIN', 'wp_users user', 'user.id=post.post_author');		
		$query->join('LEFT JOIN', "(
			    SELECT post_parent, MIN( id ) AS first_image_id
			    FROM wp_posts
			    WHERE post_type='attachment'
			        AND post_mime_type LIKE 'image/%'
			    GROUP BY post_parent
			    ) image_latest", 'post.id=image_latest.post_parent');
		$query->join('LEFT JOIN', 'wp_posts image_detail', 'image_detail.id=image_latest.first_image_id');
		$query->where(['post.post_status' => 'publish', 'post.post_type' => 'post']);
		$query->orderBy('post_date DESC');
		$query->limit($n);
		return $query->all(\Yii::$app->dbblog);
	}

	/**
     * Get this weeks' posts from Wordpress db
     * 
     * @return array
     */
	public function getWeeksPosts() 
	{
		$query = (new \yii\db\Query());
		$query->select(' 
			    post.id AS post_id,
			    post.post_author AS post_author,
			    post.post_date AS post_date,
			    post.post_title,
			    post.post_content AS post_content,
			    post.guid AS post_url,
			    user.display_name AS author_name');
		$query->from(['post' => 'wp_posts']);
		$query->join('LEFT JOIN', 'wp_users user', 'user.id=post.post_author');		
		$query->where(['post.post_status' => 'publish', 'post.post_type' => 'post']);
		$query->andWhere('post_date>DATE_SUB(NOW(), INTERVAL 7 DAY)');
		$query->orderBy('post_date DESC');
		return $query->all(\Yii::$app->dbblog);
	}

	/**
     * Get comments related to $postIds
     * 
     * @param array $postIds
     * @return array reindexed as ['comment_post_id' => 'COUNT(comment_id)']
     */
	public function getComments($postIds) 
	{
		$query = (new \yii\db\Query());
		$query->select('COUNT(comment_id), comment_post_id')
			->from('wp_comments')
			->where('comment_post_id IN(' . implode(',',$postIds) .')')
			->andWhere(['comment_approved' => 1])
			->groupBy('comment_post_id');
		$array = $query->all(\Yii::$app->dbblog);
		
		return ArrayHelper::index($array, 'comment_post_id');
	}
}