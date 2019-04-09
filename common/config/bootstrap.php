<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@blog', dirname(dirname(__DIR__)) . '/blog');

// Images
Yii::setAlias('@images', '/images');
Yii::setAlias('@img.flag', '/images/flag');
Yii::setAlias('@img.profile', '/images/profile');
Yii::setAlias('@img.network', '/images/network');
Yii::setAlias('@img.site', '/images/site');
Yii::setAlias('@img.user-area', '/images/user-area');