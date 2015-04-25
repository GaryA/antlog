<?php
/* @var $this yii\web\View */
$this->title = 'AntLog 3.0';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>AntLog 3.0</h1>

        <p class="lead">Welcome to AntLog</p>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Robots</h2>

                <p>Use this link to view and administer robots</p>

                <p><a class="btn btn-default" href="index.php?r=robot">Robots</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Teams</h2>

                <p>Use this link to view and administer teams</p>

                <p><a class="btn btn-default" href="index.php?r=team">Teams</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Events</h2>

                <p>Use this link to view and administer events</p>

                <p><a class="btn btn-default" href="index.php?r=event">Events</a></p>
			</div>
            <div class="col-lg-4">
                <h2>Entrants</h2>

                <p>Use this link to view and administer entrants to the current event</p>

                <p><a class="btn btn-default" href="index.php?r=entrant">Entrants</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Fights</h2>

                <p>Use this link to run the fights of the current competition</p>

                <p><a class="btn btn-default" href="index.php?r=fights">Fights</a></p>
            </div>
        </div>

    </div>
</div>
