<?php

function csv_to_array($filename='', $delimiter=',')
        {
            if(!file_exists($filename) || !is_readable($filename))
                return FALSE;
            $header = NULL;
            $data = array();
            if (($handle = fopen($filename, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                    if(!$header)
                        $header = $row;
                    else
                        $data[$row[0]] = array_combine($header, $row);
                }
                fclose($handle);
            }
            return $data;
        }

        function img_replace($path){
            $array_replace=[' '=>'',"'"=>'','é'=>'e','è'=>'e'];
            $temp_path=$path;
            foreach($array_replace as $k => $v){
                $temp_path=str_replace($k,$v,$temp_path);
            }
            $temp_path='img/'.strtolower($temp_path).'.jpg';

            if(file_exists($temp_path)){
                $img_path=$temp_path;
            }else{
                $img_path='';
            }
            return $img_path;
        }

        function generate_sitemap(){
            $time = explode(" ",microtime());
            $time = $time[1];
            // include class
            include 'SitemapGenerator.php';
            // create object
            $sitemap = new SitemapGenerator("http://www.aupasdecourses.com/", "../sitemap/");
            // will create also compressed (gzipped) sitemap
            $sitemap->createGZipFile = true;
            // determine how many urls should be put into one file
            $sitemap->maxURLsPerSitemap = 10000;
            // sitemap file name
            $sitemap->sitemapFileName = "sitemap.xml";
            // sitemap index file name
            $sitemap->sitemapIndexFileName = "sitemap-index.xml";
            // robots file name
            $sitemap->robotsFileName = "robots.txt";
            
            $noms_quartiers=array('Paris_1er');
            for($i=2;$i<=20;$i++){
                array_push($noms_quartiers,'Paris_'.$i.'e');
            }
            array_push($noms_quartiers,'Boulogne','Levallois-Perret','Issy-Les-Moulineaux','Montrouge','Vincennes');

            $urls=array();
            foreach($noms_quartiers as $nom){
                $temp=array("http://www.aupasdecourses.com/quartiers/".$nom, date('c'),'daily','1');
                array_push($urls,$temp);
            }

            // add many URLs at one time
            $sitemap->addUrls($urls);
            // add urls one by one
            try {
                // create sitemap
                $sitemap->createSitemap();
                // write sitemap as file
                $sitemap->writeSitemap();
                // update robots.txt file
                $sitemap->updateRobots();
                // submit sitemaps to search engines
                //$result = $sitemap->submitSitemap("yahooAppId");
                // shows each search engine submitting status
                echo "<pre>";
                //print_r($result);
                echo "</pre>";
                
            }
            catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
            echo "Memory peak usage: ".number_format(memory_get_peak_usage()/(1024*1024),2)."MB";
            $time2 = explode(" ",microtime());
            $time2 = $time2[1];
            echo "<br>Execution time: ".number_format($time2-$time)."s";

        }