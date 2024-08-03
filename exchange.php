<!DOCTYPE html>
<html lang="en">
<?php
    date_default_timezone_set("Asia/Kolkata");
    session_start();
    if(!isset($_SESSION['id'])){
        header("location:login.html");
    }
    if(isset($_GET['coin']) && isset($_GET['coinx'])){
        $coin=$_GET['coin'];
        $coinx=$_GET['coinx'];
    }else{
        $coin='BTC';
        $coinx='Bitcoin';
    }
    $chart=$coin.'USDT';
    $id=$_SESSION['id'];
    $con=mysqli_connect('localhost','root','','cryptopanel');
    if($con){
        $result=mysqli_query($con,"select * from user where id= '$id';");
        $row=mysqli_fetch_array($result);
        if($row!=null){
            if($row['isRestricted']=="1")
            { 
                header("location:php/display.php?title=RESTRICTED USER!&msg=");
            }
        }    
        $result=mysqli_query($con,"select * from portfolio where uid='$id';");
        if($result!=null){
        $result=mysqli_fetch_array($result);
        $assets=json_decode($result['assets'],true);
        }else{
            echo mysqli_error($con);
        }
        $result1=mysqli_query($con,"select * from orders where uid='$id';");
        if($result1!=null){
            $orders=null;
            while($row=mysqli_fetch_array($result1)){
                $orders[]=$row;
            }
        }
        $result2=mysqli_query($con,"select * from orders_history where uid='$id';");
        if($result2!=null){
            $ordersHistory=null;
            while($row1=mysqli_fetch_array($result2)){
                $ordersHistory[]=$row1;
            }
        }
    }
?>

<head class="crypt-dark">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cryptopanel - Cryptocurrency Trading Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/icons.css">
    <link rel="stylesheet" href="css/ui.css">
</head>

<body class="crypt-dark">
    <form>
        <input type="hidden" id="chartcoin" value="<?php echo $chart?>">
        <input type="hidden" id="assetcoin" value="<?php echo $coin?>">
        <input type="hidden" id="userassets" value=<?php echo $result['assets']?>>
        <input type="hidden" id="orders" value='<?php echo json_encode($orders,JSON_FORCE_OBJECT)?>'>
        <input type="hidden" id="ordersHistory" value='<?php echo json_encode($ordersHistory,JSON_FORCE_OBJECT)?>'>
    </form>
    <header>
        <div class="container-full-width">
            <div class="crypt-header">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5">
                        <div class="row">
                            <div class="col-xs-2">
                                <a href="exchange.html">
                                <div class="crypt-logo"><img src="images/logo.png" alt=""></div>
                            </div>
                            <!-- <here> -->
                            <div class="col-xs-2" style="padding-left:30px;">
                                <ul class="crypt-heading-menu fright">
                                    <li class="crypto-has-dropdown"><a href="exchange.php?coin=<?php echo $coin;?>&coinx=<?php echo $coinx?>"><h6><?php echo $coin."-".$coinx;?></h6></a>
                                        <ul class="crypto-dropdown">
                                            <li><a href="exchange.php?coin=BTC&coinx=Bitcoin">BTC-Bitcoin</a></li>
                                            <li><a href="exchange.php?coin=ETH&coinx=Ethereum">ETH-Ethereum</a></li>
                                            <li><a href="exchange.php?coin=BNB&coinx=BinanceCoin">BNB-BinanceCoin</a></li>
                                            <li><a href="exchange.php?coin=XRP&coinx=Ripple">XRP-Ripple</a></li>
                                            <li><a href="exchange.php?coin=SOL&coinx=Solana">SOL-Solana</a></li>
                                            <li><a href="exchange.php?coin=DOT&coinx=Polkadot">DOT-Polkadot</a></li>
                                            <li><a href="exchange.php?coin=ADA&coinx=Cardano">ADA-Cardano</a></li>
                                            <li><a href="exchange.php?coin=LUNA&coinx=Terra">LUNA-Terra</a></li>
                                            <li><a href="exchange.php?coin=SHIB&coinx=ShibaInu">SHIB-ShibaInu</a></li>
                                            <li><a href="exchange.php?coin=DOGE&coinx=DogeCoin">DOGE-DogeCoin</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-none d-md-block d-lg-block">
                        <ul class="crypt-heading-menu fright">
                            <!-- <li><a href="market-overview.html">Overview</a></li>
                            <li><a href="marketcap.html">Market Cap</a></li>
                            <li><a href="trading.html">Trading</a></li> -->
                            <li><a href="withdrawl.php">Wallet</a></li>
                            <li class="crypt-box-menu menu-red"><a href="php/logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <div class="row sm-gutters">
            <!-- ########## -->
            <?php
                $context  = stream_context_create(
                    array(
                      "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                      )
                  ));
                $coin_stats_string=array('BTC'=>'bitcoin','ETH'=>'ethereum','BNB'=>'binancecoin','XRP'=>'ripple','SOL'=>'solana','DOT'=>'polkadot','ADA'=>'cardano','LUNA'=>'terra-luna','SHIB'=>'shiba-inu','DOGE'=>'dogecoin');
                $fetch_coin=$coin_stats_string[$coin];
                $response=file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=$fetch_coin&order=market_cap_desc&per_page=100&page=1&sparkline=false&price_change_percentage=24h",false,$context);
                $response=json_decode($response,JSON_OBJECT_AS_ARRAY);
            ?>
            <!-- ########## -->
            <div class="col-md-6 col-lg-6 col-xl-9 col-xxl-10">
                <div class="crypt-gross-market-cap mt-4">
                    <div class="row">
                        <div class="col-3 col-sm-6 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <p>Market capitalisation</p>
                                    <p class="crypt-up"><?php echo '$'.$response[0]['market_cap']?></p>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <p>24H Change</p>
                                    <?php
                                    if($response[0]['price_change_24h']<0){
                                        echo "<p class='crypt-down'>".$response[0]['price_change_24h']." ".$response[0]['price_change_percentage_24h']."%</p>";
                                    }else{
                                        echo "<p class='crypt-up'>".$response[0]['price_change_24h']." ".$response[0]['price_change_percentage_24h']."%</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-sm-2 col-md-3 col-lg-2">
                            <p>24H High</p>
                            <p class="crypt-up"><?php echo '$'.$response[0]['high_24h']?></p>
                        </div>
                        <div class="col-3 col-sm-2 col-md-3 col-lg-2">
                            <p>24H Low</p>
                            <p class="crypt-down"><?php echo '$'.$response[0]['low_24h']?></p>
                        </div>
                        <div class="col-3 col-sm-2 col-md-3 col-lg-2">
                            <p>Market cap rank</p>
                            <p class="crypt-up">&nbsp;<?php echo $response[0]['market_cap_rank']?></p>
                        </div>
                    </div>
                </div>
                <div class="tradingview-widget-container mb-3">
                    <div id="crypt-candle-chartext" style="height:400px;"></div>
                </div>
                <!-- <div id="depthchart" class="depthchart h-40 crypt-dark-segment"></div> -->
                
                <!-- HTML -->
                <div id="chartdiv"></div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3 col-xxl-2">
                <div class="crypt-market-status mt-4">
                    <ul class="nav nav-tabs">
                        <li role="presentation"><a href="#history" class="active" data-toggle="tab">Recent News</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="history">
                            <?php $new = mysqli_query($con,"SELECT * FROM news ORDER BY id DESC ");
                                for($i=0;$i<7;$i++){
                                    if($res = mysqli_fetch_array($new)){  
                                        echo "<div class=\"crypto-panel-block\">";
                                        echo "<div class=\"crypto-panel-date\">";
                                        echo "<p>".date('M, d h:i',strtotime($res['date']))."</p></div>";
                                        echo "<div class=\"crypto-panel-title\">";
                                        echo "<h6>".$res['title']."</h6></div>";
                                        echo "<div class=\"crypto-panel-desc\">";
                                        echo "<p>".$res['content']."</p>";
                                        echo "<hr></div></div>";
                                    }
                               }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row sm-gutters">
            <div class="col-xl-5">
                <div class="crypt-boxed-area">
                    <h6 class="crypt-bg-head"><b class="crypt-up">BUY</b> / <b class="crypt-down">SELL</b></h6>
                    <div class="row no-gutters" style="height:45vh">
                        <div class="col-md-6">
                            <div class="crypt-buy-sell-form">
                                <p>Buy <span class="crypt-up"><?php echo $coin;?></span> <span class="fright">Available: <b class="crypt-up"><?php echo $assets['USDT'];?> USDT</b></span></p>
                                <div class="crypt-buy">
                                    <form action="php/buy.php" method="post">
                                        <input type="hidden" name="coin" value="<?php echo $coin?>">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Price</span> </div>
                                            <input type="number" name="buyPrice" id="buyPrice" class="form-control" step="any">
                                            <div class="input-group-append"> <span class="input-group-text">USDT</span> </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Amount</span> </div>
                                            <input type="number" name="buyAmount" id="buyAmount" class="form-control" step="any">
                                            <div class="input-group-append"> <span class="input-group-text">USDT</span> </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Total</span> </div>
                                            <input type="text" name="buyTotal" id="buyTotal" class="form-control" readonly>
                                            <div class="input-group-append"> <span class="input-group-text"><?php echo $coin;?></span> </div>
                                        </div>
                                        <input type="hidden" id="assetsBuy" name="assets" value=<?php echo $result['assets']?>>
                                        <!-- <div>
                                            <p>Fee: <span class="fright">100%x0.2=0.02</span></p>
                                        </div>
                                        <div class="text-center mt-3 mb-3 crypt-up">
                                            <p>You will approximately pay</p>
                                            <h4>0.09834 BTC</h4></div> -->
                                        <!-- <div class="menu-green"> -->
                                            <!-- <a href="#" class="crypt-button-green-full">Buy</a> -->
                                            <input class="crypt-button-green-full" name="buyButton" id="buyButton" type="submit" value="Buy">
                                        <!-- </div> -->
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="crypt-buy-sell-form">
                                <p>Sell <span class="crypt-down"><?php echo $coin;?></span> <span class="fright">Available: <b class="crypt-down"><?php echo $assets[$coin];?> <?php echo $coin;?></b></span></p>
                                <div class="crypt-sell">
                                    <form action="php/sell.php" method="post">
                                        <input type="hidden" name="coin" value="<?php echo $coin?>">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Price</span> </div>
                                            <input type="number" name="sellPrice" id="sellPrice" class="form-control" step="any">
                                            <div class="input-group-append"> <span class="input-group-text">USDT</span> </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Amount</span> </div>
                                            <input type="number" name="sellAmount" id="sellAmount" class="form-control" step="any">
                                            <div class="input-group-append"> <span class="input-group-text"><?php echo $coin;?></span> </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend"> <span class="input-group-text">Total</span> </div>
                                            <input type="text" name="sellTotal" id="sellTotal" class="form-control" readonly>
                                            <div class="input-group-append"> <span class="input-group-text">USDT</span> </div>
                                        </div>
                                        <input type="hidden" id="assetsSell" name="assets" value=<?php echo $result['assets']?>>
                                        <!-- <div>
                                            <p>Fee: <span class="fright">100%x0.2=0.02</span></p>
                                        </div>
                                        <div class="text-center mt-3 mb-3 crypt-down">
                                            <p>You will approximately pay</p>
                                            <h4>0.09834 BTC</h4></div> -->
                                        <!-- <div><a href="#" class="crypt-button-red-full">Sell</a></div> -->
                                        <!-- <div class="menu-green"> -->
                                            <!-- <a href="#" class="crypt-button-green-full">Buy</a> -->
                                            <input class="crypt-button-red-full" name="sellButton" id="sellButton" type="submit" value="Sell">
                                        <!-- </div> -->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div>
                    <div class="crypt-market-status">
                        <div>
                            <ul class="nav nav-tabs">
                                <li role="presentation"><a href="#active-orders" class="active" data-toggle="tab">Active Orders</a></li>
                                <li role="presentation"><a href="#closed-orders" data-toggle="tab">Closed Orders</a></li>
                                <li role="presentation"><a href="#balance" data-toggle="tab">Balance</a></li>
                                <a href="<?php echo"exchange.php?coin=$coin&coinx=$coinx"?>" class="fright" style="color:#3898ff">Update orders? last updated: <?php echo date("H:i:s")?></a>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="active-orders" style="height:45vh">
                                    <table class="table table-striped">
                                        <thead >
                                            <tr>
                                                <th scope="col">Coin</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Buy/sell</th>
                                                <th scope="col">Price USDT</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="active-orders-body">
                                        
                                        </tbody>
                                    </table>
                                    <!-- <div class="no-orders text-center p-160"><img src="images/empty.svg" alt="no-orders"></div> -->
                                </div>
                                <div role="tabpanel" class="tab-pane" id="closed-orders" style="height:45vh">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Coin</th>
                                                <th scope="col">Placed Time</th>
                                                <th scope="col">Resolved Time</th>
                                                <th scope="col">Buy/sell</th>
                                                <th scope="col">Price USDT</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="closed-orders-body">
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="balance" style="height:45vh">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Currency</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Last price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $balance=json_decode($result['assets'],JSON_OBJECT_AS_ARRAY);
                                            unset($balance['USDT']);
                                            $prices=mysqli_fetch_array(mysqli_query($con,"select * from prices where id=1"),MYSQLI_ASSOC);
                                            foreach($balance as $bcoin => $bvalue){
                                                echo "<tr>
                                                    <td>$bcoin</td>
                                                    <td>$bvalue</td>
                                                    <td>{$prices[$bcoin."USDT"]}</td>
                                                    </tr>";
                                            }
                                        ?>    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>

    </footer>
    <script src="js/bundle.js"></script>
	<!-- <script src="js/s3.tradingview.com/tv.js"></script> -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script>
        if (document.getElementById('crypt-candle-chartext')){
            let coin = document.getElementById('chartcoin');
            new TradingView.widget(
            {
            "autosize": true,
            // "width": 1520,
            // "height": 400,
            "symbol": coin.value,
            "interval": "1",
            "timezone": "Asia/Kolkata",
            "theme": "dark",
            "style": "3",
            "locale": "in",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "withdateranges": true,
            "range": "1D",
            "hide_side_toolbar": false,
            "allow_symbol_change": true,
            "details": true,
            "hotlist": true,
            "calendar": true,
            "show_popup_button": true,
            "popup_width": screen.width,
            "popup_height": screen.height,
            "container_id": "crypt-candle-chartext"
            });
        }
    </script>
    <script src="extjs/orders.js"></script>
    <script src="extjs/validate/exchange.js"></script>

    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <script>
am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
var root = am5.Root.new("chartdiv");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
]);

// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
var chart = root.container.children.push(
  am5xy.XYChart.new(root, {
    focusable: true,
    panX: false,
    panY: false,
    wheelX: "none",
    wheelY: "none"
  })
);

// Chart title
var title = chart.plotContainer.children.push(am5.Label.new(root, {
  text: "Price (BTC/ETH)",
  fontSize: 20,
  fontWeight: "400",
  x: am5.p50,
  centerX: am5.p50
}))

// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  categoryField: "value",
  renderer: am5xy.AxisRendererX.new(root, {
    minGridDistance: 70
  }),
  tooltip: am5.Tooltip.new(root, {})
}));

xAxis.get("renderer").labels.template.adapters.add("text", function(text, target) {
  if (target.dataItem) {
    return root.numberFormatter.format(Number(target.dataItem.get("category")), "#.####");
  }
  return text;
});

var yAxis = chart.yAxes.push(
  am5xy.ValueAxis.new(root, {
    maxDeviation: 0.1,
    renderer: am5xy.AxisRendererY.new(root, {})
  })
);

// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/

var bidsTotalVolume = chart.series.push(am5xy.StepLineSeries.new(root, {
  minBulletDistance: 10,
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "bidstotalvolume",
  categoryXField: "value",
  stroke: am5.color(0x00ff00),
  fill: am5.color(0x00ff00),
  tooltip: am5.Tooltip.new(root, {
    pointerOrientation: "horizontal",
    labelText: "[width: 120px]Ask:[/][bold]{categoryX}[/]\n[width: 120px]Total volume:[/][bold]{valueY}[/]\n[width: 120px]Volume:[/][bold]{bidsvolume}[/]"
  })
}));
bidsTotalVolume.strokes.template.set("strokeWidth", 2)
bidsTotalVolume.fills.template.setAll({
  visible: true,
  fillOpacity: 0.2
});

var asksTotalVolume = chart.series.push(am5xy.StepLineSeries.new(root, {
  minBulletDistance: 10,
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "askstotalvolume",
  categoryXField: "value",
  stroke: am5.color(0xf00f00),
  fill: am5.color(0xff0000),
  tooltip: am5.Tooltip.new(root, {
    pointerOrientation: "horizontal",
    labelText: "[width: 120px]Ask:[/][bold]{categoryX}[/]\n[width: 120px]Total volume:[/][bold]{valueY}[/]\n[width: 120px]Volume:[/][bold]{asksvolume}[/]"
  })
}));
asksTotalVolume.strokes.template.set("strokeWidth", 2)
asksTotalVolume.fills.template.setAll({
  visible: true,
  fillOpacity: 0.2
});

var bidVolume = chart.series.push(am5xy.ColumnSeries.new(root, {
  minBulletDistance: 10,
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "bidsvolume",
  categoryXField: "value",
  fill: am5.color(0x000000)
}));
bidVolume.columns.template.set("fillOpacity", 0.2);

var asksVolume = chart.series.push(am5xy.ColumnSeries.new(root, {
  minBulletDistance: 10,
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "asksvolume",
  categoryXField: "value",
  fill: am5.color(0x000000)
}));
asksVolume.columns.template.set("fillOpacity", 0.2);

// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
  xAxis: xAxis
}));
cursor.lineY.set("visible", false);

// Data loader
function loadData() {
  am5.net.load("https://poloniex.com/public?command=returnOrderBook&currencyPair=BTC_ETH&depth=50").then(function(result) {
    var data = am5.JSONParser.parse(result.response);
    parseData(data);
  }).catch(function() {
    // Failed to load
    // Using drop-in data
    parseData({
      "asks" : [ [ "0.07070", 1.0 ], [ "0.07071", 1.654 ], [ "0.07076", 0.61 ], [ "0.07077", 1.2 ], [ "0.07093", 0.584 ], [ "0.07095", 0.005 ], [ "0.07098", 0.01 ], [ "0.07100", 0.653 ], [ "0.07105", 6.0 ], [ "0.07107", 0.002 ], [ "0.07110", 0.022 ], [ "0.07113", 0.001 ], [ "0.07115", 0.001 ], [ "0.07117", 0.001 ], [ "0.07119", 0.001 ], [ "0.07123", 0.001 ], [ "0.07124", 0.002 ], [ "0.07125", 0.001 ], [ "0.07127", 0.001 ], [ "0.07129", 0.001 ], [ "0.07130", 0.001 ], [ "0.07131", 0.001 ], [ "0.07133", 0.001 ], [ "0.07135", 0.002 ], [ "0.07137", 0.001 ], [ "0.07139", 0.001 ], [ "0.07141", 0.001 ], [ "0.07143", 0.001 ], [ "0.07145", 0.001 ], [ "0.07147", 0.004 ], [ "0.07148", 6.311 ], [ "0.07149", 0.001 ], [ "0.07150", 10.03 ], [ "0.07151", 0.001 ], [ "0.07153", 0.001 ], [ "0.07155", 0.001 ], [ "0.07157", 0.001 ], [ "0.07159", 0.001 ], [ "0.07161", 0.001 ], [ "0.07162", 0.238 ], [ "0.07163", 0.001 ], [ "0.07164", 0.584 ], [ "0.07165", 0.541 ], [ "0.07167", 0.001 ], [ "0.07169", 0.001 ], [ "0.07171", 0.001 ], [ "0.07173", 0.001 ], [ "0.07175", 0.017 ], [ "0.07177", 0.001 ], [ "0.07179", 0.001 ] ],
      "bids" : [ [ "0.07060", 1.001 ], [ "0.07059", 1.544 ], [ "0.07056", 0.61 ], [ "0.07053", 0.002 ], [ "0.07048", 1.2 ], [ "0.07040", 0.05 ], [ "0.07031", 0.663 ], [ "0.07024", 0.005 ], [ "0.07020", 5.99 ], [ "0.07010", 0.022 ], [ "0.07006", 0.001 ], [ "0.07005", 0.003 ], [ "0.07000", 1.0 ], [ "0.06993", 0.002 ], [ "0.06990", 6.15 ], [ "0.06989", 0.519 ], [ "0.06986", 0.001 ], [ "0.06983", 0.024 ], [ "0.06980", 0.031 ], [ "0.06978", 0.01 ], [ "0.06977", 0.81 ], [ "0.06975", 0.053 ], [ "0.06970", 0.022 ], [ "0.06967", 0.531 ], [ "0.06962", 0.017 ], [ "0.06955", 0.004 ], [ "0.06953", 0.002 ], [ "0.06951", 0.031 ], [ "0.06950", 10.0 ], [ "0.06933", 0.301 ], [ "0.06932", 0.606 ], [ "0.06931", 0.022 ], [ "0.06929", 0.015 ], [ "0.06924", 2.48 ], [ "0.06923", 0.5 ], [ "0.06922", 0.2 ], [ "0.06921", 0.5 ], [ "0.06918", 0.03 ], [ "0.06915", 0.001 ], [ "0.06912", 0.069 ], [ "0.06911", 0.002 ], [ "0.06905", 0.003 ], [ "0.06900", 20.39 ], [ "0.06899", 0.002 ], [ "0.06897", 0.242 ], [ "0.06886", 0.808 ], [ "0.06880", 0.026 ], [ "0.06872", 1.0 ], [ "0.06868", 0.005 ], [ "0.06862", 0.584 ] ],
      "isFrozen" : "0",
      "postOnly" : "0",
      "seq" : 67767369
    })
  });
}

function parseData(data) {
  var res = [];
  processData(data.bids, "bids", true, res);
  processData(data.asks, "asks", false, res);
  xAxis.data.setAll(res);
  bidsTotalVolume.data.setAll(res);
  asksTotalVolume.data.setAll(res);
  bidVolume.data.setAll(res);
  asksVolume.data.setAll(res);
}

loadData();

setInterval(loadData, 30000);


// Function to process (sort and calculate cummulative volume)
function processData(list, type, desc, res) {

  // Convert to data points
  for(var i = 0; i < list.length; i++) {
    list[i] = {
      value: Number(list[i][0]),
      volume: Number(list[i][1]),
    }
  }

  // Sort list just in case
  list.sort(function(a, b) {
    if (a.value > b.value) {
      return 1;
    }
    else if (a.value < b.value) {
      return -1;
    }
    else {
      return 0;
    }
  });

  // Calculate cummulative volume
  if (desc) {
    for(var i = list.length - 1; i >= 0; i--) {
      if (i < (list.length - 1)) {
        list[i].totalvolume = list[i+1].totalvolume + list[i].volume;
      }
      else {
        list[i].totalvolume = list[i].volume;
      }
      var dp = {};
      dp["value"] = list[i].value;
      dp[type + "volume"] = list[i].volume;
      dp[type + "totalvolume"] = list[i].totalvolume;
      res.unshift(dp);
    }
  }
  else {
    for(var i = 0; i < list.length; i++) {
      if (i > 0) {
        list[i].totalvolume = list[i-1].totalvolume + list[i].volume;
      }
      else {
        list[i].totalvolume = list[i].volume;
      }
      var dp = {};
      dp["value"] = list[i].value;
      dp[type + "volume"] = list[i].volume;
      dp[type + "totalvolume"] = list[i].totalvolume;
      res.push(dp);
    }
  }

}

}); // end am5.ready()
</script>

</body>
</html>