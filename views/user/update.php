<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\UpdateForm */

$this->title = 'Update Team: ' . $model->team_name;
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->team_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?><div class="team-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-update']); ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'email')->input('email') ?>
                <?= $form->field($model, 'team_name') ?>
                <div class="form-group">
                    <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'update-button']) ?>
                    </div>
            <?php ActiveForm::end(); ?>
			<?php
			if (!User::isUserAdmin())
			{
				echo Html::a('Delete', ['/user/delete', 'id' => $model->id],
				[
					'class' => 'btn btn-danger',
					'data' =>
					[
						'confirm' => 'Are you sure you want to delete this team/user?',
						'method' => 'post',
					],
				]);
			}
			?>
            </div>
    </div>
</div>
