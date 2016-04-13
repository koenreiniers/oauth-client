<?php
require "bootstrap.php";

if(isset($_POST['erase'])) {
    session_destroy();
    header("Location: client.php");
}
if(isset($_POST['expire'])) {
    $accessToken = $tokenStorage->getAccessToken();
    $expiresAt = new \DateTime("2000-05-14 10:10:10");
    $newToken = new \Kr\OAuthClient\Token\BearerToken($accessToken->getToken(), $expiresAt);
    $tokenStorage->setAccessToken($newToken);
}

$endpoints = [
    "products", "stores", "categories", "eans", "snapshots"
];


$uri = "";

$response = null;
if(isset($_POST['method']))
{

    $url = "/" . $_POST['endpoint'] . $_POST['uri'];
    $response = $oauth->request($_POST['method'], $url);
}

$accessToken = $tokenStorage->getAccessToken();

?>
<?php if($accessToken === null): ?>
    Not authorized: <a href="authorize.php">Authorize client</a>
<?php else: ?>
    <form method="POST">
        <button name="erase">Erase credentials</button> <button name="expire">Expire access token</button>
    </form>
    <form method="POST">
        Access token: <?php echo $accessToken->getToken(); ?><br/>
        Expires at: <?=$accessToken->getExpiresAt()->format("Y-m-d H:i:s");?><br/>
        <br/>
        <select name="method">
            <option value="GET">GET</option>
        </select>
        <select name="endpoint">
            <?php foreach($endpoints as $endpoint): ?>
                <option value="<?=$endpoint;?>">/<?=$endpoint;?></option>
            <?php endforeach; ?>
        </select> <input name="uri" value="" placeholder="/1 bijvoorbeeld"><br/>

        <button>Send</button>
    </form>
    <?php if($response !== null): ?>
        <?php echo $response->getBody(); ?>
    <?php endif; ?>
<?php endif; ?>