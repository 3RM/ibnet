<?php
use yii\helpers\Html;
?>

<footer class="footer">
    <div class="footer-container">
        <div>
            &copy; Independent Baptist Network <?php echo date('Y') ?>
        </div>
        <div class="footer-links">
            <?= HTML::a('Privacy', ['/site/privacy']) ?>
            <?= HTML::a('Terms', ['/site/terms']) ?>
            <?= HTML::a('Beliefs', ['/site/beliefs']) ?>
            <?= Html::a('About', ['/site/about']) ?>
            <?= HTML::a('Contact', ['/site/contact']); ?>
            <?= Yii::$app->user->isGuest ? HTML::a('Register', ['/site/register']) : NULL; ?>
        </div>
        <div>
            Designed by <a href="http://ifbdesign.com" target="_blank">IFBDesign</a> & <a href="https://ibnet.org" target="_blank">IBNet</a>
        </div>
    </div>
    <div class="footer-social">
        <?= Html::a('<span class="social social-facebook"></span>', 'https://www.facebook.com/ibnet.org/', ['target' => '_blank']) ?>
        <?= Html::a('<span class="social social-twitter"></span>', 'https://twitter.com/ibnet_org', ['target' => '_blank']) ?>
        <?= Html::a('<span class="social social-linked-in"></span>', 'https://www.linkedin.com/company/independent-baptist-network/', ['target' => '_blank']) ?>
        <?= Html::a('<span class="social social-e-mail-envelope"></span>', 'mailto:admin@ibnet.org') ?>
    </div>
</footer>