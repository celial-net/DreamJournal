<!--Start of Navbar-->
<div class="navbar navbar-expand-lg bg-dark navbar-dark sticky-top">
	<a class="navbar-brand" href="/dream">
		<img alt="Pipeline" src="/dist/assets/img/logo.svg" />
	</a>
	<div class="d-flex align-items-center">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="d-block d-lg-none ml-2">
			<div class="dropdown">
				<a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<img alt="Image" src="/dist/assets/img/avatar-male-4.jpg" class="avatar" />
				</a>
				<?php
				if(!Yii::$app->getUser()->getIsGuest())
				{
				?>
				<div class="dropdown-menu dropdown-menu-right">
					<a href="#" class="dropdown-item"><?=Yii::$app->getUser()->getIdentity()->name ?? 'Anonymous'?>'s Profile</a>
					<a href="/user/settings" class="dropdown-item">Account Settings</a>
					<a href="/user/logout" class="dropdown-item">Log Out</a>
				</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>

	<div class="collapse navbar-collapse flex-column" id="navbar-collapse">
		<ul class="navbar-nav d-lg-block">
			<li class="nav-item">
				<a class="nav-link" href="/dream">Home</a>
			</li>
		<?php
		if(!Yii::$app->getUser()->getIsGuest())
		{
			?>
			<li class="nav-item">
				<a class="nav-link" href="/dream">My Dreams</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-2" aria-controls="submenu-2">Analysis</a>
				<div id="submenu-2" class="collapse">
					<ul class="nav nav-small flex-column">
						<li class="nav-item">
							<a class="nav-link" href="/analysis/week">Dreams by Day of Week</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="/analysis/month">Dreams by Month</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="/analysis/category">Dreams by Category</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="/analysis/concept">Dreams by Concept</a>
						</li>
					</ul>
				</div>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="/search">Search</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-3" aria-controls="submenu-3">Data</a>
				<div id="submenu-3" class="collapse">
					<ul class="nav nav-small flex-column">
						<li class="nav-item">
							<a class="nav-link" href="/data/import">Import</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/data/export">Export</a>
						</li>
					</ul>
				</div>
			</li>

			<?php
			if(Yii::$app->getUser()->can('manageAdminData'))
			{
				?>
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-4" aria-controls="submenu-3">Admin</a>
					<div id="submenu-4" class="collapse">
						<ul class="nav nav-small flex-column">
							<li class="nav-item">
								<a class="nav-link" href="/dreamcategory">Dream Categories</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="/dreamconcept">Dream Concept</a>
							</li>
						</ul>
					</div>
				</li>
				<?php
			}
		}
		else
		{
			?>
			<li class="nav-item">
				<a class="nav-link" href="/user/login">Login</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="/user/register">Register</a>
			</li>
			<?php
		}
		?>
			<li class="nav-item">
				<a class="nav-link" href="#">About</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="#">Contact</a>
			</li>
			<li class="nav-item">
				<div class="form">
					<form action="/dream" method="get">
						<input type="text" class="form-control" name="search" placeholder="Search for dreams..."/>
					</form>

				</div>
			</li>
		</ul>
		<hr>
		<div class="d-none d-lg-block w-100">
			<span class="text-small text-muted">Quick Links</span>
			<ul class="nav nav-small flex-column mt-2">
				<li class="nav-item">
					<a href="#" class="nav-link">Lucid Dreams</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link">etc</a>
				</li>
			</ul>
			<hr>
		</div>
	</div>

<?php
if(!Yii::$app->getUser()->getIsGuest())
{
	?>
	<div class="d-none d-lg-block">
		<div class="dropup">
			<a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<img alt="Image" src="/dist/assets/img/avatar-male-4.jpg" class="avatar"/>
			</a>
			<div class="dropdown-menu">
				<a href="#" class="dropdown-item"><?= Yii::$app->getUser()->getIdentity()->name ?? 'Anonymous' ?>'s
					Profile</a>
				<a href="/user/settings" class="dropdown-item">Account Settings</a>
				<a href="/user/logout" class="dropdown-item">Log Out</a>
			</div>
		</div>
	</div>
	<?php
}
?>
</div>

<!--End of Navbar-->