<?php
/** @var string $weekActive */
/** @var string $monthActive */
/** @var string $yearActive */
/** @var string $allActive */
?>
<div class="container">
	<br>
	<div class="row content-list-head">
		<div class="col-auto">
			<h3>Recent Dreams</h3>
		</div>
		<div class="col-md-auto">
			<ul class="nav nav-pills nav-small">
				<li class="nav-item">
					<a class="nav-link <?=$allActive?>" href="/dream?period=all">All</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=$weekActive?>" href="/dream?period=week">Week</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=$monthActive?>" href="/dream?period=month">Month</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=$yearActive?>" href="/dream?period=year">Year</a>
				</li>
			</ul>
		</div>
	</div>
	<hr>

	<ol class="list-group list-group-activity">
        <?php
        /** @var \app\models\dj\Dream[] $dreams */
		foreach($dreams as $dream)
        {
            if(!$dream)
            {
                echo '<hr>';
                continue;
            }

            ?>
            <li class="list-group-item">
                <div class="media align-items-center">
                    <ul class="avatars">
                        <li>
                            <div class="avatar bg-primary">
                                <i class="material-icons">single_bed</i>
                            </div>
                        </li>
                    </ul>
                    <div class="media-body">
                        <div>
                            <a href="/dream/view/<?=$dream->getId()?>" class="A-filter-by-text"><?=$dream->getTitle()?></a>
                        </div>
                        <span class="text-small"><?=$dream->getFormattedDate()?></span>
                    </div>
                </div>
            </li>
            <?php
        }
        ?>
	</ol>
</div>