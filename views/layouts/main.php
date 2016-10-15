<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\User;
use app\models\Lock;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        	if (Yii::$app->params['antlog_env'] == 'local')
        	{
        		$host = gethostname();
        		$ip = gethostbyname($host);
        		$ipMessage = "($ip)";
        	}
        	else
        	{
        		$ipMessage = '';
        	}
        	if (Lock::isLocked())
        	{
        		$ipMessage .= ' Database is locked';
        	}
            NavBar::begin([
                'brandLabel' => '<img src = "' . Yii::getAlias('@web') .
            	'/awslogo-sm-xprnt.png" style = "float: left; margin-top: -15px; margin-right: 5px;">' .
            	Yii::$app->name. ' ' . Yii::$app->version . ' (' .
            	Yii::$app->params['antlog_env'] .
            	') <small>' . $ipMessage . '</small>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'About', 'url' => ['/site/about']],
            	['label' => 'Help', 'url' => ['site/help']],
            ];
            if (Yii::$app->user->isGuest)
            {
                $menuItems[] = ['label' => 'New Team', 'url' => ['/site/signup']];
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            }
            else
            {
            	if (User::isUserAdmin() && Yii::$app->params['antlog_env'] == 'local')
            	{
            		$menuItems[] = ['label' => 'Import', 'url' => ['db/import']];
            	}
            	if ((!Yii::$app->user->isGuest && Yii::$app->params['antlog_env'] == 'web') || User::isUserAdmin())
            	{
            		//$menuItems[] = ['label' => 'Export', 'url' => ['db/export']];
            		$menuItems[] = ['label' => 'Export', 'url' => ['/site/download']];
            	}
                $menuItems[] =
                [
                	'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);

            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Team BeligerAnt <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
