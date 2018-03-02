<?
/**
 * To generate link for the navbar
 * @param $pageName - page name
 * @param $name - name of the link
 */
function _navlist($pageName,$name){
    ?>
    <li <?= $pageName[3] == $name.".php" ? "class='nav-item active'" : "class=nav-item" ?> >
        <a class="nav-link" href="<?= URL_BASE . $name .".php" ?>"><?=ucfirst($name)?></a>
    </li>
    <?
}
?>
<nav class="navbar navbar-toggleable-md scrolling-navbar navbar-light teal lighten-4">
    <div class="container">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarList" aria-controls="navbarList" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?=URL_BASE?>index.php">
            <i class="fa fa-gamepad" style="color:orange"></i>
            Game<span style="color:orange">Con</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarList">
            <ul class="navbar-nav mr-auto">
                <?
                if (isset($_SESSION) && $_SESSION["loggedIn"] == true && isset($_COOKIE['loggedIn'])) {
                    _navlist($pageName,"product");
                    _navlist($pageName,"cart");
                    if (_checkAdmin()) {
                        _navlist($pageName,"admin");
                    }
                    _navlist($pageName,"logout");
                }else{
                    _navlist($pageName,"login");
                    _navlist($pageName,"signup");
                }
                ?>
            </ul>
        </div>
</nav>