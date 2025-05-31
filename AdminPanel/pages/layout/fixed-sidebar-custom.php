<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="index.php" class="brand-link">

        <span class="brand-text font-weight-light">ERB Tournament App</span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['userName']?></a>
            </div>
            <div class='info'>
                <a href="logout.php" type='button' class='badge badge-danger'>Log Out</a>
            </div>
        </div>

        <div class="form-inline" style='display:none;'>
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>


        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="users.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='users.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>

                <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='add-tournament.php' || basename($_SERVER['PHP_SELF'])=='tournaments.php'){  echo 'menu-is-opening menu-open'; } ?>">
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-chess"></i>
                        <p>
                            Tournaments
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" <?php if(basename($_SERVER['PHP_SELF'])=='add-tournament.php'){  echo 'style="display: block;"'; } ?>>
                        <li class="nav-item">
                            <a href="add-tournament.php" data-toggle="open" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='add-tournament.php'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add New Tournament</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="tournaments.php?t_status=live" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='tournaments.php' && $_GET['t_status'] == 'live'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ongoing Tournaments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="tournaments.php?t_status=completed" data-toggle="open" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='tournaments.php' && $_GET['t_status'] == 'completed'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tournament Results</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="tournaments.php?t_status=available" data-toggle="open" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='tournaments.php' && $_GET['t_status'] == 'available'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Upcoming Tournaments</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="offline-payments.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='offline-payments.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Offline Payments
                        </p>
                    </a>
                </li>
                
                <li class="nav-item <?php if(basename($_SERVER['PHP_SELF'])=='add-game.php' || basename($_SERVER['PHP_SELF'])=='games.php'){  echo 'menu-is-opening menu-open'; } ?>">
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-chess"></i>
                        <p>
                            Games
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" <?php if(basename($_SERVER['PHP_SELF'])=='add-game.php'){  echo 'style="display: block;"'; } ?>>
                        <li class="nav-item">
                            <a href="add-game.php" data-toggle="open" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='add-game.php'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add New Game</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="games.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='games.php'){  echo 'active'; } ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Games</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="withdraw-requests.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='withdraw-requests.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-star"></i>
                        <p>
                            Withdraw Requests
                        </p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="transactions.php?id=0" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='transactions.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            Transactions
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="send-notification.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='send-notification.php'){  echo 'active'; } ?>">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            Send Notification
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <!--<a href="main-data.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='main-data.php'){  echo 'active'; } ?>">-->
                    <!--    <i class="nav-icon fas fa-database"></i>-->
                    <!--    <p>-->
                    <!--        Main Data-->
                    <!--    </p>-->
                    <!--</a>-->
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
