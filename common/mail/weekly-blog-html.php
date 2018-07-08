<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<div>
    <h4><?= $title ?></h4>
    <p><?= $message ?></p>
    <ul>
    	<?php 
    		$i = 0;
    		foreach ($posts as $post) {
    			echo '<li style="list-style:none"><em>' . Html::a($posts[$i]['post_title'] . '</em> by ' . $posts[$i]['author_name'], $posts[$i]['post_url'], ['target' => '_blank', 'style' => 'text-decoration:none']) . '</li>'; 
    			$i++;
    		}
    	?>
    </ul>
    <p>To change your email preferences or to unsubsribe from this list, <?= $emailPrefLink ?>.</p>

</div>
