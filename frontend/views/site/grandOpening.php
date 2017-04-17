<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Grand Opening';
Url::remember();
?>
<div class="site-about">
    <h1><?= $this->title ?></h1>

    <div class="media">
        <div class="media-body">
            <p>Welcome to the grand opening of the newly updated IBNet.org!  Read our story <?= Html::a('here', ['/site/about'], ['target' => '_blank']) ?>. This first phase of the launch primarily implements a directory of churches, ministries, and individuals that will put independent Baptists “on the map.” Use this as a tool to make your ministry known and to discover other like-minded ministries and individuals. You may notice that this directory has some uncommon features that set it apart from other similar online directories, and that are designed to make it more functional and accurate. One example that we are especially excited about is the extensive cross-linking of profiles to allow you to “surf” across ministries in the directory. Also, we have many other unique and beneficial features planned for future updates. But we won’t spill the beans just yet. Stay tuned…</p>

            <h2>How can IBNet benefit you?</h2>
            <h3>Find</h3>
            
            <p>This website will help you find ministries and individuals.  It will raise your awareness of IB activity anywhere you want to look.  How many print ministries or IB schools are in your state, or within 100 miles of you, or in your country?  What churches are available at your next travel destination?  The search and browse features make these and many other questions available with only a few clicks.</p>

            <p>The flip side of finding is being found.  We live in an age where people will look at your web presence before they ever darken your doorway.  Some ministries have no web presence, and therefore can only be known through word of mouth.  Your ministry website may appear in search engines for very narrow searches.  But now you can leverage IBNet to drive traffic to your site, or to serve as your web presence if don't have one.   We keep you informed of how many visitors view your profile in a given time period.</p>

            <p>Create a free profile for you or your ministry and enjoy much more exposure to online visitors than you otherwise could.  We track:</p>

            <h4>Organizations</h4>
        
            <ul>
                <li>Church</li>
                <li>Association</li>
                <li>Fellowship</li>
                <li>Mission Agency</li>
                <li>Music Ministry</li>
                <li>Print Ministry</li>
                <li>Camp</li>
                <li>School</li>
                <li>Special Ministry</li>
            </ul>

            <h4>Individuals</h4>

            <ul>
                <li>Pastor (Senior, Associate, Assistant, Youth, Emeritus, Music, Elder)</li>
                <li>Evangelist</li>
                <li>Missionary (Church planter, Bible translator, Medical)</li>
                <li>Chaplain</li>
                <li>Ministry Staff (e.g. School Principle, Director, Office Assistant, etc.)</li>
            </ul>

            <h3>Connect</h3>
            <p>This website will help you connect with other likeminded ministries and individuals.   Listing all of the possible reasons and ways to connect would be impossible.  But this website facilitates connections by making access to others easier than ever before.  As mentioned above, we have some very exciting, upcoming features planned that will further aide in helping you connect with others.  We hope this website becomes a valuable tool for you in life and ministry.</p>

        </div>
        <div class="media-right">
            <?= HTML::img('@web/images/grand-opening-lg.jpg', ['class' => 'media object', 'style' => 'margin-left: 10pt']) ?>
        </div>
    </div>

    <p>&nbsp;</p>

</div>
