<?php include('header.php') ?>


<?php 
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/controllers/SearchController.php";
$search = new SearchController();

    if(isset($_GET["term"])){
        $term = $_GET["term"];
    }else{
        exit("you must enter a search term");
    }

    $type = (isset($_GET["type"]))? $_GET["type"] : "web";
    $page  = (isset($_GET["page"]))? $_GET["page"] : 1;
    
?>
<body>
    <div class="wrapper ">
     
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="/">
                        <img src="./public/images/scopr.png" >
                    </a>
                </div>
                <div class="searchContainer">
                  <form action="search" method="GET">
                    <div class="searchBarContainer">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <input type="text" name="term" id="" value='<?php echo "$term" ?>' class="searchBox">
                        <button type="submit" class="searchButton">
                            <img src="./public/images/search.png"> 
                        </button>
                    </div>
                  </form>  
                </div>
            </div>

            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<?php echo $type == 'web' ? 'active':'' ?>">
                        <a href='<?php echo "search?term=".$term. "&type=web"?>' class= "">Web</a>
                    </li>
                    <li class="<?php echo $type == 'images' ? 'active':'' ?>">
                        <a href='<?php echo "search?term=".$term. "&type=images"?>' class="">Pics</a>
                    </li>
                    <li class="<?php echo $type == 'news' ? 'active':'' ?>">
                        <a href='<?php echo "search?term=".$term. "&type=news" ?>' class="">News</a>
                    </li>
                </ul>
            </div>  
        </div>

    <div class="mainResultsSection">
        <?php 
        if($_GET['type'] == "web"){
            $pageLimit = 20;
            $fullResults = $search->getLinks($page, $pageLimit, $term);
         
        }else{
            $pageLimit = 30;
            $fullResults = $search->getImages($page, $pageLimit, $term);
            //var_dump($fullResults);
        }
           
            if($fullResults){ 
                $num = $fullResults['count'];
                $data = $fullResults['search'];
                $result = $search->formatResultsNum($num);
                echo "<p class='resultsCount'>$num $result found</p>";
            }else{
                die("<p class='resultsCount'>Nothing found</p>");
            }
            
        ?>

     
        <?php 
        if($_GET['type'] != "web"){
            $count = 0;
            echo "
            <div class='imageResults'>";  
            
        }
                foreach($data as $row){
                    if($_GET['type'] == "web"){
                        $id =  $row['id']; 
                        $url =  $row['url'];
                        $title =  (isset($row['title'])) ? $row['title']: preg_replace('/\s+?(\S+)?$/', '', substr($row['description'], 0, 101));
                        $description  =  (isset($row['description'])) ? preg_replace('/\s+?(\S+)?$/', '', substr($row['description'], 0, 150))."...": $row['keywords'];
                        $term = "";
                        echo "
                        <div class='siteResults'>
                            <div class='resultsContainer'>
                                <h3 class='title'>
                                    <a class='result' data-id='$id' href='$url'>
                                        $title
                                    </a>
                                </h3>
                                <span class='description'>$description</span><br/>
                                <span class='url'>$url</span>
                            </div>
                        </div>";
                    }else{

                        $id =  $row['id']; 
                        $imageUrl =  $row['raw_image_link'];
                        $description =   (isset($row['image_alt']) && $row['image_alt'] !="") ? $row['image_alt'] : $row['website_url'];
                        $siteUrl = $row['website_url'];
                        $count++;
                        $term = "";
                        $imageClass = "image".$count;
                        echo "
                            <div class='gridItem $imageClass'>
                                <a href='$imageUrl' data-fancybox data-caption='$description' data-siteurl='$siteUrl'>
                                    <script>
                                        $(document).ready(function(){
                                            loadImage(\"$imageUrl\",\"$imageClass\");
                                        });
                                    </script> 
                                 <span class='details'>$description</span>
                                </a>
                                    
                            </div>";
                       
                    }
                } 
                if($_GET['type'] != "web"){
                    echo "</div>";  
                }
                
        ?> 
     
    </div>
    <div class="paginationContainer">
        <div class="pageButtons">
            <div class="pageNumberContainer"> 
            
            </div>
                <?php
                    $term = $_GET['term'];
                    $pagesToShow = 11;
                    $numPages =  ceil($num / $pageLimit);
                    $pagesLeft = min($pagesToShow, $numPages);
                    $currentPage =  $page - floor($pagesToShow / 2); 
                    if($currentPage < 1){
                        $currentPage = 1;
                    }
                    if($currentPage + $pagesLeft > $numPages + 1){
                        $currentPage = $numPages-$pagesLeft;
                    }
                    while($pagesLeft != 0 && $currentPage <= $numPages){
                        if($currentPage == $page){
                            echo "<div class='pageNumberContainer '>
                                    <span class='pageNumber'>$currentPage</span>

                            </div>";
                        }else{
                            echo " <div class='pageNumberContainer '>
                                        <a href='search?term=$term&type=$type&page=$currentPage'>
                                        <span class='pageNumber'>$currentPage</span>
                                        </a>
                            </div>";
                        }
                        

                        $currentPage++;
                        $pagesLeft--;
                    }
                ?>



            <div class="pageNumberContainer">
            
            </div>
        </div>
        
    </div>
    </div>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
    <script type="text/javascript" src="./public/scripts/script.js" > </script>
</body>

<?php include('footer.php') ?>