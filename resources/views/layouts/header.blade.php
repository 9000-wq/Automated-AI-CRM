<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="CRM">
	<meta name="keywords" content="CRM">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="{{asset('img/icons/icon-48x48.png')}}" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/" />

	<title>CRM Dashboard</title>

	<link href="{{asset('css/app.css')}}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
	<!-- DataTables Core + Bootstrap 5 + Responsive -->
	<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
	<link rel="stylesheet" href="//cdn.datatables.net/responsive/3.0.4/css/responsive.bootstrap5.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

	<style>
		nav li {
			border-bottom: 2px solid white;
			padding-bottom: 10px;
			padding-top: 10px;
		}

		.sidebar-link {
			color: white !important;
		}


		.dt-search {
			text-align: end !important;
		}

		.dt-paging {
			text-align: end !important;
			margin-top: 10px !important;
		}


		.sidebar-item:hover {
			background-color: white;
			transition: 2s;
		}

		.sidebar-item:hover a,
		.sidebar-item:hover a i,
		.sidebar-item:hover a span {
			color: black !important;
			transition: 2s;
		}




		.active {
			background-color: white;
		}

		.active a {
			color: black !important;
		}

		.active i {
			color: black !important;
		}

		.active span {
			color: black !important;
		}


		table thead {
			background-color: #3b65ea !important;
		}

		table thead th {
			color: white !important;
			text-align: left !important;
		}

		table td {
			text-align: left !important;
		}
	</style>
</head>

<body>

	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div style="padding: 0px;background-color: #3b65ea;font-size: medium;" class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="index.html">
					<span class="align-middle">CRM System</span>
				</a>

				<ul class="sidebar-nav">


					<li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('dashboard')}}">
							<i class="fas fa-tachometer-alt	"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>
					<a href="#" class="sidebar-link"><strong>CRM</strong></a>
					<li class="sidebar-item {{ request()->routeIs('home.account') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('home.account') }}">
							<i class="fas fa-user-circle"></i>
							<span class="align-middle">Account</span>
						</a>
					</li>


					@if(Auth::user()->user_role == 'super admin' or Auth::user()->user_role == 'admin')
						<li
							class="sidebar-item  {{ request()->routeIs('leads.index') || request()->routeIs('leads.create') || request()->routeIs('leads.show') || request()->routeIs('leads.edit') || request()->routeIs('leadcontact') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('leads.index')}}">
								<i class="fas fa-magnet"></i> <span class="align-middle">Lead Managment</span>
							</a>
						</li>
					@endif


					
					<li
						class="sidebar-item  {{ request()->routeIs('Opportunities') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('Opportunities')}}">
							<i class="fas fa-dollar-sign"></i> <span class="align-middle">Opportunities</span>
						</a>
					</li>

					<li
						class="sidebar-item  {{ request()->routeIs('contacts.index') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('contacts.index')}}">
							<i class="fas fa-address-book"></i> <span class="align-middle">Contacts</span>
						</a>
					</li>
				<li
						class="sidebar-item  {{ request()->routeIs('create.call') && count(request()->route()->parameters()) === 0 ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('create.call')}}">
							<i class="fas fa-phone"></i> <span class="align-middle">Calls</span>
						</a>
					</li>

					@if(Auth::user()->user_role == 'super admin')

						<li class="sidebar-item  {{ request()->routeIs('plans.index') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('plans.index')}}">
								<i class="fas fa-list-alt"></i> <span class="align-middle">Plans Managment</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs('manageprices') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('manageprices')}}">
								<i class="fas fa-money-bill-alt	"></i> <span class="align-middle">Price Managment</span>
							</a>
						</li>


					@endif

					<li class="sidebar-item  {{ request()->routeIs('users.index') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('users.index')}}">
							<i class="fas fa-users"></i> <span class="align-middle">Users</span>
						</a>
					</li>

					<li class="sidebar-item ">
						<a class="sidebar-link" href="#"
							onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="fas fa-sign-out-alt"></i> <span class="align-middle">Logout</span>
						</a>
					</li>

				</ul>

			</div>
		</nav>

		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
					<i class="hamburger align-self-center"></i>
				</a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">


						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
								data-bs-toggle="dropdown">
								<i class="align-middle" data-feather="settings"></i>
							</a>

							<button id="fullscreenBtn" class="btn" style="outline: none;border: none;font-size: 20px;" title="Toggle Fullscreen">
								<i id="fullscreenIcon" style="color: #0d6efd;" class="fas fa-expand-arrows-alt mt-2"></i>
							</button>



							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
								data-bs-toggle="dropdown">
								<img src="{{asset('img/avatars/myavatar.jpg')}}" class="avatar img-fluid rounded me-1"
									alt="Charles Hall" /> <span class="text-dark">{{Auth::user()->name}}</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1"
										data-feather="user"></i> Profile</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1"
										data-feather="pie-chart"></i> Analytics</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.html"><i class="align-middle me-1"
										data-feather="settings"></i> Settings & Privacy</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1"
										data-feather="help-circle"></i> Help Center</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"
									onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
									out</a>
								<!-- Hidden Logout Form -->
								<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
									@csrf
								</form>
							</div>
						</li>
					</ul>
				</div>
			</nav>