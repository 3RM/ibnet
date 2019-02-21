<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'About';
Url::remember();
?>
<div class="site-about">
    <div class="row">
        <div class="col-md-8">
            <h1>Our Story</h1>

            <h2>Independence is a Fundamental Principle</h2>
            <p>It might sound oxymoronic to say that independence is a rallying cry for unaffiliated Baptists.   But truly we can all unite around our independence!  Independence has been called a fundamental principle in Baptist belief and practice.  Francis Wayland stated,</p>
            <blockquote>
                <p>The Baptists have ever believed in the entire and absolute independence of the churches. By this, we mean that every church of Christ, that is, every company of believers united together according to the laws of Christ, is wholly independent of every other; that every church is perfectly capable of self-government; and that, therefore, no one acknowledges any higher authority, under Christ, than itself;</p>
            </blockquote>
            <p>But while we remain resolutely independent ecclesiastically, our individual and corporate lives are nevertheless intertwined through varying forms of relationships and interactions which are enabled increasingly more through the vast network of technology and social media.  Independence doesn’t need to mean isolation.</p>

            <h2>IBNet Helps Independents Connect</h2>
            <p>IBNet was born out of the need for a central gathering place of independent Baptists on the internet.  Baptists can be found all across the web and on most social media platforms, but there has been no central marketplace in which to meet and interrelate.  IBNet endeavors to meet this challenge.</p>
            <p>We firmly uphold the foundational principle of independence and the Baptist distinctive of the primacy and autonomy of the local church.  We also understand the importance of being able to reach out and interact with others of like faith and practice for all of the reasons, small or great, that have brought independent Baptists together in the past.  Our desire is that you find this website a useful ministry tool for connecting with like-minded churches, organizations, and individuals.</p>

            <h2>In the Beginning</h2>
            <p>IBNet was started by Dr. David Capetz in 1995.  Dr. Capetz has been involved with Christian ministry and education his entire adult life.  He served on staff at Maranatha Baptist University, on administrative staff and as adjunct professor at Central Seminary, and as Executive Vice President, Dean, and Professor at Canadian Baptist Bible College.  In what could be termed the “stone-ages” of the internet, Dr. Capetz was visionary in promoting digital resources and encouraging independent Baptist ministries to harness the power of the rapidly growing internet.  IBNet was one of the fruits of those efforts.  Some of you reading this may remember the original ibnet.org.</p>

            <h2>The Present</h2>
            <p><?= HTML::img('@img.site/Steve&Dave.jpg', ['class' => 'img-rounded', 'align' => 'left', 'style' => 'margin-right: 10pt']) ?><?= HTML::a('Steve McKinley', ['profile/missionary', 'urlLoc' => 'ireland', 'urlName' => 'mckinley', 'id' => 1], ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?> is a missionary to the Republic of Ireland and a friend of Dr. Capetz.  He too understood the need and the great potential for an online marketplace for independent Baptists.  He took up the mantle of Dr. Captetz’ work, and relaunched IBNet in 2017.<br><br><br><br></p>

            <h2>Long-Term Vision</h2>
            <p>We see tremendous potential for IBNet along two lines: First, to serve as an informal online gathering place for Baptists, and second, as a platform and outlet for promoting a Baptist (i.e. biblical) worldview in the larger online community.  Look for new features and content in the future to further these ends.</p>

            <p>&nbsp;</p>
        </div>
    </div>
</div>
