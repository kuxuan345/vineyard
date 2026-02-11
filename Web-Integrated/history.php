<!DOCTYPE html>
<html lang="en">
<?php
    // Include the header
    include('header.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <title>HIP Z History</title>
    <!-- Link to External CSS -->
    <link rel="stylesheet" href="css/history.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <div class="history-style">
        <h1>OUR HISTORY</h1>


        <div class="year">
            <h2>Since 2000</h2>
        </div>


        <!-- First Paragraph with Image Pair -->
        <div class="history-section">
            <div class="history-set">
                <?php
                // First paragraph
                $paragraph1 = "The origins of HIPZ trace back to the year 2000, when the seeds of its vision were first sown. The spirit of HIPZ was long nurtured by a reverence for traditional winemaking and a passion for purity. Over the years, this dream quietly matured, much like a fine wine, until it found its full expression in a company devoted to crafting the most authentic and exquisite wine experiences.";
                $firstLetter = substr($paragraph1, 0, 1);
                $restOfText = substr($paragraph1, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>


            <div class="history-img">
                <img src="img/building2.jpg" alt="History Image 1">
            </div>
        </div>


        <!-- Second Row: Two Paragraphs Side by Side -->
        <div class="history-row">
            <div class="history-set">
                <?php
                // Second paragraph
                $paragraph2 = "HIPZ, a premium liquor sales company, was established with the vision of delivering the purest and most authentic wine experiences to its customers. Founded by four passionate entrepreneurs, HIPZ emerged as a subsidiary of Qupick, a renowned company committed to quality and innovation.";
                $firstLetter = substr($paragraph2, 0, 1);
                $restOfText = substr($paragraph2, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>
            <div class="history-set">
                <?php
                // Third paragraph
                $paragraph3 = "The journey of HIPZ began with a shared belief among its founders: that wine is more than a drink; it is an art form that should remain true to its roots. They envisioned a company that upheld traditional winemaking practices while setting new standards for quality and purity in the industry.";
                $firstLetter = substr($paragraph3, 0, 1);
                $restOfText = substr($paragraph3, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>
        </div>


        <!-- Third Paragraph with Image Pair -->
        <div class="history-section">
            <div class="history-img1">
                <img src="img/barrel1.jpg" alt="History Image 2">
            </div>
            <div class="history-set">
                <?php
                // Fourth paragraph
                $paragraph4 = "From the outset, HIPZ dedicated itself to meticulous craftsmanship. Every step of the production process, from vineyard selection to bottling, is carried out with the utmost precision. The founders insisted on strict quality control measures, ensuring that every bottle reflects their commitment to excellence.";
                $firstLetter = substr($paragraph4, 0, 1);
                $restOfText = substr($paragraph4, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>
        </div>


        <!-- Fourth Row: Two Paragraphs Side by Side -->
        <div class="history-row">
            <div class="history-set">
                <?php
                // Fifth paragraph
                $paragraph5 = "Unlike mass-produced alternatives, HIPZ's wines are crafted entirely by hand, a testament to the company's dedication to preserving the essence of winemaking tradition.";
                $firstLetter = substr($paragraph5, 0, 1);
                $restOfText = substr($paragraph5, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>
            <div class="history-set">
                <?php
                // Sixth paragraph
                $paragraph6 = "As a subsidiary of Qupick, HIPZ benefits from the parent company’s vast resources and expertise while maintaining its unique identity. This partnership enables HIPZ to reach a global audience, sharing its passion for pure, high-quality wines with connoisseurs and casual drinkers alike.";
                $firstLetter = substr($paragraph6, 0, 1);
                $restOfText = substr($paragraph6, 1);
                echo "<p><span class='dropcap'>$firstLetter</span>$restOfText</p>";
                ?>
            </div>
        </div>


    </div>


</body>
</html>
