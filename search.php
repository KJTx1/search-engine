<html>
<head>
	<title>Gulugulu</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<style>
   div svg {
      transform: scale3d(1.5, 1.5, 1.5);
   }
</style>

<body style="margin-left: 50px; margin-right: 50px">
   
   <div id="box" style="position: relative;">
      <h1 id="logo" style="text-align: center; margin-top: 250px; font-size: 4em;"><i class="bi bi-google" style="color:#1266F1"></i><span style='color: #DB4437'>u</span><span style='color: #ffce44'>l</span><span style='color: #DB4437'>u</span><span style='color: #1266F1'>g</span><span style='color: #DB4437'>u</span><span style='color: #0F9D58'>l</span><span style='color: #DB4437'>u</span></h1>
      <br>
      <div class="d-flex justify-content-center">
         <h1 onClick="window.location.href=window.location.href" id="small-logo" style="font-size: 2em; margin-left: -40px; margin-right: 20px; display: none"><i class="bi bi-google" style="color:#1266F1"></i><span style='color: #DB4437'>u</span><span style='color: #ffce44'>l</span><span style='color: #DB4437'>u</span><span style='color: #1266F1'>g</span><span style='color: #DB4437'>u</span><span style='color: #0F9D58'>l</span><span style='color: #DB4437'>u</span></h1>
         <form id="form" class="input-group" action="search.php" method="post" style="max-width: 650px; z-index: 1">
            <input class="form-control" type="text" size=40 name="search_string" placeholder="Search any headphone or audio related topic..." value="<?php echo $_POST["search_string"];?>"/>
            <button type="submit" class="btn btn-primary" name="action" value="Search"><i class="bi bi-search"></i> Search</button>
            <button type="submit" class="btn btn-success" name="action" value="Try it!" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Suggest search word based on your search history"><i class="bi bi-lightbulb"></i> Try It</button>
         </form>
      </div>
   </div>

   <div style="position: fixed; bottom: 0; left: 0; width: 100%; background-color: #E0E0E0; z-index: 1">
      <div style="margin: 10px; display: flex; justify-content: center;">
         <a style="display:inline-block; margin-right:10px" rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">
            <img alt="Creative Commons License" style="border-width:0;" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" />
         </a>
         <p style="display:inline-block; margin-bottom:0; height: 31px; line-height: 31px; text-align: center;">
            This work is licensed under a 
            <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
         </p>
      </div>
   </div>

   <?php

      $filename = "terms.txt";
      $fh = fopen($filename,'r');
      $content = fread($fh, filesize($filename));
      $terms = explode(" ", $content);

      $uni_terms = array_unique($terms);

      if (sizeof($uni_terms) >= 2) {
         echo "<div style=\"display: flex; align-items: center; justify-content: center\">";
         echo "<h5 style=\"margin-bottom: 0\">Recent Searched Words (Up To 3):</h5>";
         $three_terms_array = explode(" ", implode(" ", array_slice($uni_terms, 0, 3)));
         foreach ($three_terms_array as $each) {
            if (trim($each) != '') {
            echo " <span style=\"margin-left: 5px\" class=\"badge rounded-pill bg-warning text-dark\">".$each."</span>";
            }
         }
         echo "</div>";
      }

      echo "<br>";

      fclose($fh);

      $search_string = '';

      echo "<br>";

      $radTerm = '';

      if (isset($_POST["search_string"]) && $_POST['action'] === "Try it!") {
         if (sizeof($uni_terms) < 2) {
            echo "<div class=\"alert alert-danger d-flex align-items-center\" role=\"alert\" style=\"max-width: 750px; justify-content: center; margin: auto;\">
            <i style=\"margin: 10px\" class=\"bi bi-exclamation-triangle-fill\"></i>
               <div>
               Please feed our search engine with your search data to enable your \"Try It\" feature!
               </div>
            </div>";
            exit();
         }

         $num = -1;
         $radTerm = "";
         do {
            $num = rand(0, sizeof($uni_terms) - 2);
            $radTerm = $uni_terms[$num];
            $search_string = $radTerm;
         } while (trim($radTerm) === "");
         
      }

      if ($radTerm === "") {
         $radTerm = $_POST["search_string"];
      }

      if (isset($_POST["search_string"]) && trim($_POST["search_string"]) === '' && $_POST['action'] === "Search") {
         echo "<div class=\"alert alert-warning d-flex align-items-center\" role=\"alert\" style=\"max-width: 400px; justify-content: center; margin: auto;\">
                  <i class=\"bi bi-emoji-smile-upside-down\" style=\"margin: 10px\"></i>
                  <div>
                  Please use a least one word to search!
                  </div>
               </div>";
      } else if (isset($_POST["search_string"])) {
         if ($_POST['action'] === "Search") {
            $search_string = $_POST["search_string"];
         }
         $qfile = fopen("query.py", "w");

         $logfile = fopen("logs.txt", "a");
         $log = $search_string . " " . date("Y/m/d") . " " . date("h:i:sa") . " " . $_SERVER['REMOTE_ADDR'] . "\n";
         fwrite($logfile, $log);
         fclose($logfile);

         fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
         fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
         fwrite($qfile, "index = pt.IndexFactory.of(\"./reddit_index/data.properties\")\n");
         fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

         for ($i=0; $i<10; $i++)
         {
            fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i]))\n");
            fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
         }
         
         fclose($qfile);

         exec("ls | nc -u 127.0.0.1 11016");
         sleep(5);

         echo "<li class=\"list-group-item\" style=\"max-width: 1000px; justify-content: center; margin: auto; background-color: #E0E0E0;\">Showing results for \"" . $radTerm . "\":</li>\n";

         $stream = fopen("output", "r");

         $line=fgets($stream);

         $count = 0;

         $myStrings = array();

         while(($line=fgets($stream))!=false)
         {
            $clean_line = preg_replace('/\s+/',',',$line);
            $record = explode("./", $clean_line);
            $line = fgets($stream);

            if (!array_key_exists($line, $myStrings))
            {
               $count = $count + 1;
               $myStrings[$line] = $line;
               $url = rtrim($record[1], "index.html,");

               echo "<a target=\"_blank\" rel=\"noopener noreferrer\" href=\"http://$url\" class=\"searchResults list-group-item list-group-item-action\" style=\"background-color: #ECEFF1; max-width: 1000px; justify-content: center; margin: auto;\">".$count.'. '.$line."</a>";
            }
         }

         if ($_POST['action'] === "Search") {
            if ($count == 0) {
               echo "<div class=\"alert alert-warning d-flex align-items-center\" role=\"alert\" style=\"max-width: 1000px; justify-content: center; margin: auto;\">
                        <i class=\"bi bi-emoji-frown\" style=\"margin: 10px\"></i>
                        <div>
                        No Matching Result Found...
                        </div>
                     </div>";
            } else {  
               $termfile = fopen($filename, "wa+");
               fwrite($termfile, $search_string . " " . implode(" ", $terms));
               fclose($termfile);
            }
         }

         fclose($stream);
         
         exec("rm query.py");
         exec("rm output");
         
      } 
      
   ?>

   <div id="wordCloud" style="justify-content: center; margin: auto; margin-bottom: 100px; padding: 0; border: 0; z-index: -1;"></div>

   <script>
      if (document.getElementsByClassName("searchResults").length > 0) {
         document.getElementById("logo").style.display = "none";
         document.getElementById("small-logo").style.display = "inline";
         document.getElementById("wordCloud").classList.add("list-group-item");
         document.getElementById("wordCloud").style.maxWidth = "1000";
         document.getElementById("wordCloud").style.background = "url(./holo.jpeg) center";
         document.getElementById("wordCloud").style.backgroundRepeat = "no-repeat";
         document.getElementById("wordCloud").style.backgroundSize = "cover";
      }
   </script>

   <script type="module">

   import {Runtime, Inspector} from "https://cdn.jsdelivr.net/npm/@observablehq/runtime@4/dist/runtime.js";
   import define from "https://api.observablehq.com/@d3/word-cloud.js?v=3";

   let resultObj = document.getElementsByClassName("searchResults")
      let words = new Array()
      Array.from(resultObj).forEach((element) => {
         words = words.concat(element.innerText.split(" ").slice(1))
      })
   const main = new Runtime().module(define, name => {
   if (name === "chart") return new Inspector(document.querySelector("#wordCloud"));
   });
   main.redefine("words", words);

   </script>

</body>
</html>
