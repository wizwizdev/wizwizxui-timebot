<?php
include 'includ/header.php';
?>
<?php
$sql_admins = "SELECT * FROM admins";
$result_admins = $conn->query($sql_admins);
$row_admins = $result_admins->fetch_assoc();
$lang_file = 'langs/lang_' . $row_admins['lang'] . '.php';
if (file_exists($lang_file)) {
    include($lang_file);
}
?>
<ul class="mt-4">
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="index.php"
        >
            <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
            >
                <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                ></path>
            </svg>
            <span class="ml-4"><?php echo $_LANG['Dashboard'] ?></span>
        </a>
    </li>
</ul>


<ul>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="orders.php"
        >
            <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
            >
                <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                ></path>
            </svg>
            <span class="ml-4"><?php echo $_LANG['Orders'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="servers.php"
        >
            <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
            >
                <path
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                ></path>
            </svg>
            <span class="ml-4"><?php echo $_LANG['Servers'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="category.php"
        >
            <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 id="Outline" viewBox="0 0 24 24" width="20" height="20">
                <path d="M7,0H4A4,4,0,0,0,0,4V7a4,4,0,0,0,4,4H7a4,4,0,0,0,4-4V4A4,4,0,0,0,7,0ZM9,7A2,2,0,0,1,7,9H4A2,2,0,0,1,2,7V4A2,2,0,0,1,4,2H7A2,2,0,0,1,9,4Z"/>
                <path d="M7,13H4a4,4,0,0,0-4,4v3a4,4,0,0,0,4,4H7a4,4,0,0,0,4-4V17A4,4,0,0,0,7,13Zm2,7a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V17a2,2,0,0,1,2-2H7a2,2,0,0,1,2,2Z"/>
                <path d="M20,13H17a4,4,0,0,0-4,4v3a4,4,0,0,0,4,4h3a4,4,0,0,0,4-4V17A4,4,0,0,0,20,13Zm2,7a2,2,0,0,1-2,2H17a2,2,0,0,1-2-2V17a2,2,0,0,1,2-2h3a2,2,0,0,1,2,2Z"/>
                <path d="M14,7h3v3a1,1,0,0,0,2,0V7h3a1,1,0,0,0,0-2H19V2a1,1,0,0,0-2,0V5H14a1,1,0,0,0,0,2Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['category'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="singleplans.php"
        >
            <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 id="Outline" viewBox="0 0 24 24" width="20" height="20">
                <path d="M19,3H12.472a1.019,1.019,0,0,1-.447-.1L8.869,1.316A3.014,3.014,0,0,0,7.528,1H5A5.006,5.006,0,0,0,0,6V18a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V8A5.006,5.006,0,0,0,19,3ZM5,3H7.528a1.019,1.019,0,0,1,.447.1l3.156,1.579A3.014,3.014,0,0,0,12.472,5H19a3,3,0,0,1,2.779,1.882L2,6.994V6A3,3,0,0,1,5,3ZM19,21H5a3,3,0,0,1-3-3V8.994l20-.113V18A3,3,0,0,1,19,21Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['SinglePlans'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="multipleplans.php"
        >
            <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 id="Outline" viewBox="0 0 24 24" width="20" height="20">
                <path d="M19,3H12.472a1.019,1.019,0,0,1-.447-.1L8.869,1.316A3.014,3.014,0,0,0,7.528,1H5A5.006,5.006,0,0,0,0,6V18a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V8A5.006,5.006,0,0,0,19,3ZM5,3H7.528a1.019,1.019,0,0,1,.447.1l3.156,1.579A3.014,3.014,0,0,0,12.472,5H19a3,3,0,0,1,2.779,1.882L2,6.994V6A3,3,0,0,1,5,3ZM19,21H5a3,3,0,0,1-3-3V8.994l20-.113V18A3,3,0,0,1,19,21Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['MultiplePlans'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="pays.php">
            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" fill="gray" data-name="Layer 1"
                 viewBox="0 0 24 24" width="21" height="21">
                <path d="M12,9C17.934,8.844,17.933,.155,12,0c-5.934,.156-5.933,8.845,0,9Zm0-7c3.286,.059,3.285,4.942,0,5-3.285-.059-3.285-4.942,0-5Zm10.204,9.162c-1.143-.953-2.64-1.347-4.099-1.081l-3.821,.695c-.913,.166-1.707,.634-2.284,1.289-.578-.655-1.371-1.123-2.285-1.289l-3.821-.695c-1.461-.264-2.956,.128-4.098,1.081-1.142,.953-1.796,2.352-1.796,3.839v2.793c0,2.417,1.727,4.486,4.106,4.919l6.284,1.143c1.068,.194,2.151,.194,3.219,0l6.285-1.143c2.379-.433,4.105-2.502,4.105-4.919v-2.793c0-1.487-.654-2.886-1.796-3.838Zm-11.204,10.767c-.084-.012-.168-.026-.252-.041l-6.284-1.143c-1.428-.26-2.464-1.501-2.464-2.952v-2.793c0-.892,.393-1.731,1.078-2.303,.685-.573,1.59-.808,2.459-.648l3.821,.695c.952,.173,1.642,1,1.642,1.968v7.217Zm11-4.135c0,1.451-1.036,2.692-2.463,2.952l-6.285,1.143c-.084,.015-.168,.029-.252,.041v-7.217c0-.967,.69-1.795,1.642-1.968l3.821-.695c.875-.16,1.774,.077,2.46,.648,.685,.572,1.077,1.411,1.077,2.303v2.793Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['Pays'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="add-volume.php">
            <svg fill="gray" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                 id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;"
                 xml:space="preserve" width="21" height="21"><g>
                    <path d="M480,224H288V32c0-17.673-14.327-32-32-32s-32,14.327-32,32v192H32c-17.673,0-32,14.327-32,32s14.327,32,32,32h192v192   c0,17.673,14.327,32,32,32s32-14.327,32-32V288h192c17.673,0,32-14.327,32-32S497.673,224,480,224z"/>
                </g></svg>


            <span class="ml-4"><?php echo $_LANG['AddVolume'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="volume.php">
            <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="gray" viewBox="0 0 24 24" width="21" height="21">
                <path d="M22.485,10.975,12,17.267,1.515,10.975A1,1,0,1,0,.486,12.69l11,6.6a1,1,0,0,0,1.03,0l11-6.6a1,1,0,1,0-1.029-1.715Z"/>
                <path d="M22.485,15.543,12,21.834,1.515,15.543A1,1,0,1,0,.486,17.258l11,6.6a1,1,0,0,0,1.03,0l11-6.6a1,1,0,1,0-1.029-1.715Z"/>
                <path d="M12,14.773a2.976,2.976,0,0,1-1.531-.425L.485,8.357a1,1,0,0,1,0-1.714L10.469.652a2.973,2.973,0,0,1,3.062,0l9.984,5.991a1,1,0,0,1,0,1.714l-9.984,5.991A2.976,2.976,0,0,1,12,14.773ZM2.944,7.5,11.5,12.633a.974.974,0,0,0,1,0L21.056,7.5,12.5,2.367a.974.974,0,0,0-1,0h0Z"/>
            </svg>


            <span class="ml-4"><?php echo $_LANG['Volumeorders'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="discount.php">
            <svg id="Layer_1" height="21" viewBox="0 0 24 24" fill="gray" width="21" xmlns="http://www.w3.org/2000/svg"
                 data-name="Layer 1">
                <path d="m24 12a1 1 0 0 1 -2 0 10.011 10.011 0 0 0 -10-10 1 1 0 0 1 0-2 12.013 12.013 0 0 1 12 12zm-8 1a1 1 0 0 0 0-2h-2.277a2 2 0 0 0 -.723-.723v-3.277a1 1 0 0 0 -2 0v3.277a1.994 1.994 0 1 0 2.723 2.723zm-14.173-6.216a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm.173 5.216a1 1 0 1 0 -1 1 1 1 0 0 0 1-1zm10 10a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-7.779-18.793a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm3.558-2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-5.952 14.375a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm2.394 3.577a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm3.558 2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm14.394-5.943a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-2.394 3.577a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-3.558 2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['Discountcode'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="rahgozar.php">
            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" fill="gray" viewBox="0 0 24 24"
                 width="21" height="21">
                <path d="M23,12.5A1.5,1.5,0,0,1,21.5,14H18.63a3.516,3.516,0,0,1-3-1.7l-1.225-2.034-1.79,4.363,2.614,1.487A3.507,3.507,0,0,1,17,19.163V22.5a1.5,1.5,0,0,1-3,0V19.163a.5.5,0,0,0-.252-.434L9.666,16.406a3.511,3.511,0,0,1-1.427-4.322L9.5,9H7.736a.5.5,0,0,0-.447.277L5.842,12.171a1.5,1.5,0,0,1-2.684-1.342L4.605,7.935A3.483,3.483,0,0,1,7.736,6H13.36a3.516,3.516,0,0,1,3,1.7L18.2,10.758A.5.5,0,0,0,18.63,11H21.5A1.5,1.5,0,0,1,23,12.5ZM8.057,16.85a1.5,1.5,0,0,0-1.95.836A.5.5,0,0,1,5.643,18H3.5a1.5,1.5,0,0,0,0,3H5.643a3.484,3.484,0,0,0,3.25-2.2A1.5,1.5,0,0,0,8.057,16.85ZM14.5,5A2.5,2.5,0,1,0,12,2.5,2.5,2.5,0,0,0,14.5,5Z"/>
            </svg>


            <span class="ml-4"><?php echo $_LANG['Rahgozar'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="gift.php">
            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" fill="gray" data-name="Layer 1" viewBox="0 0 24 24"
                 width="21" height="21">
                <path d="M21,7H17.866A6.547,6.547,0,0,0,20,2H18c0,2.881-1.971,4.307-4.152,4.8A9.239,9.239,0,0,0,15,3,3,3,0,0,0,9,3a9.239,9.239,0,0,0,1.152,3.8C7.971,6.307,6,4.881,6,2H4A6.547,6.547,0,0,0,6.134,7H3a3,3,0,0,0-3,3v4H2V24H22V14h2V10A3,3,0,0,0,21,7ZM12,2a1,1,0,0,1,1,1,7.71,7.71,0,0,1-1,3.013A7.71,7.71,0,0,1,11,3,1,1,0,0,1,12,2ZM2,10A1,1,0,0,1,3,9h8v3H2Zm2,4h7v8H4Zm16,8H13V14h7Zm2-10H13V9h8a1,1,0,0,1,1,1Z"/>
            </svg>


            <span class="ml-4"><?php echo $_LANG['Gift'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
                  <span
                          class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                          aria-hidden="true"
                  ></span>
        <a
                class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="software.php">
            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="21"
                 height="21">
                <path d="M11.24,24a2.262,2.262,0,0,1-.948-.212,2.18,2.18,0,0,1-1.2-2.622L10.653,16H6.975A3,3,0,0,1,4.1,12.131l3.024-10A2.983,2.983,0,0,1,10,0h3.693a2.6,2.6,0,0,1,2.433,3.511L14.443,8H17a3,3,0,0,1,2.483,4.684l-6.4,10.3A2.2,2.2,0,0,1,11.24,24ZM10,2a1,1,0,0,0-.958.71l-3.024,10A1,1,0,0,0,6.975,14H12a1,1,0,0,1,.957,1.29L11.01,21.732a.183.183,0,0,0,.121.241A.188.188,0,0,0,11.4,21.9l6.4-10.3a1,1,0,0,0,.078-1.063A.979.979,0,0,0,17,10H13a1,1,0,0,1-.937-1.351l2.19-5.84A.6.6,0,0,0,13.693,2Z"/>
            </svg>
            <span class="ml-4"><?php echo $_LANG['Software'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="wizwizbackup.php">
            <svg xmlns="http://www.w3.org/2000/svg" fill="gray" id="Outline" viewBox="0 0 24 24" width="21" height="21"><path d="M18.4,7.379a1.128,1.128,0,0,1-.769-.754h0a8,8,0,1,0-15.1,5.237A1.046,1.046,0,0,1,2.223,13.1,5.5,5.5,0,0,0,.057,18.3,5.622,5.622,0,0,0,5.683,23H11a1,1,0,0,0,1-1h0a1,1,0,0,0-1-1H5.683a3.614,3.614,0,0,1-3.646-2.981,3.456,3.456,0,0,1,1.376-3.313A3.021,3.021,0,0,0,4.4,11.141a6.113,6.113,0,0,1-.073-4.126A5.956,5.956,0,0,1,9.215,3.05,6.109,6.109,0,0,1,9.987,3a5.984,5.984,0,0,1,5.756,4.28,2.977,2.977,0,0,0,2.01,1.99,5.934,5.934,0,0,1,.778,11.09.976.976,0,0,0-.531.888h0a.988.988,0,0,0,1.388.915c4.134-1.987,6.38-7.214,2.88-12.264A6.935,6.935,0,0,0,18.4,7.379Z"/><path d="M18.707,16.707a1,1,0,0,0,0-1.414l-1.586-1.586a3,3,0,0,0-4.242,0l-1.586,1.586a1,1,0,0,0,1.414,1.414L14,15.414V23a1,1,0,0,0,2,0V15.414l1.293,1.293a1,1,0,0,0,1.414,0Z"/></svg>
            <span class="ml-4"><?php echo $_LANG['Backup'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="settings.php">
            <svg xmlns="http://www.w3.org/2000/svg" fill="gray" id="Outline" viewBox="0 0 24 24" width="21"
                 height="21">
                <path d="M12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z"/>
                <path d="M21.294,13.9l-.444-.256a9.1,9.1,0,0,0,0-3.29l.444-.256a3,3,0,1,0-3-5.2l-.445.257A8.977,8.977,0,0,0,15,3.513V3A3,3,0,0,0,9,3v.513A8.977,8.977,0,0,0,6.152,5.159L5.705,4.9a3,3,0,0,0-3,5.2l.444.256a9.1,9.1,0,0,0,0,3.29l-.444.256a3,3,0,1,0,3,5.2l.445-.257A8.977,8.977,0,0,0,9,20.487V21a3,3,0,0,0,6,0v-.513a8.977,8.977,0,0,0,2.848-1.646l.447.258a3,3,0,0,0,3-5.2Zm-2.548-3.776a7.048,7.048,0,0,1,0,3.75,1,1,0,0,0,.464,1.133l1.084.626a1,1,0,0,1-1,1.733l-1.086-.628a1,1,0,0,0-1.215.165,6.984,6.984,0,0,1-3.243,1.875,1,1,0,0,0-.751.969V21a1,1,0,0,1-2,0V19.748a1,1,0,0,0-.751-.969A6.984,6.984,0,0,1,7.006,16.9a1,1,0,0,0-1.215-.165l-1.084.627a1,1,0,1,1-1-1.732l1.084-.626a1,1,0,0,0,.464-1.133,7.048,7.048,0,0,1,0-3.75A1,1,0,0,0,4.79,8.992L3.706,8.366a1,1,0,0,1,1-1.733l1.086.628A1,1,0,0,0,7.006,7.1a6.984,6.984,0,0,1,3.243-1.875A1,1,0,0,0,11,4.252V3a1,1,0,0,1,2,0V4.252a1,1,0,0,0,.751.969A6.984,6.984,0,0,1,16.994,7.1a1,1,0,0,0,1.215.165l1.084-.627a1,1,0,1,1,1,1.732l-1.084.626A1,1,0,0,0,18.746,10.125Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['Settings'] ?></span>
        </a>
    </li>
</ul>
<div class="px-6 my-2 mt-4">
    <a href="https://t.me/wizwizch" target="_blank">
        <button
                class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue"
        >
            Telegram wizwiz
            <span class="ml-2" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                       fill="#fff" width="15" height="15"><g id="_01_align_center"
                                                                                             data-name="01 align center"><path
                                d="M1.444,6.669a2,2,0,0,0-.865,3.337l3.412,3.408V20h6.593l3.435,3.43a1.987,1.987,0,0,0,1.408.588,2.034,2.034,0,0,0,.51-.066,1.978,1.978,0,0,0,1.42-1.379L23.991.021ZM2,8.592l17.028-5.02L5.993,16.586v-4Zm13.44,13.424L11.413,18h-4L20.446,4.978Z"/></g></svg>
</span>
        </button>
    </a>
</div>
<div class="px-6 my-2">
    <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank">
        <button
                class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-500 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-blue">
            Github
            <svg fill="#fff" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve" width="15" height="15"><g>
                    <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M12,0.296c-6.627,0-12,5.372-12,12c0,5.302,3.438,9.8,8.206,11.387   c0.6,0.111,0.82-0.26,0.82-0.577c0-0.286-0.011-1.231-0.016-2.234c-3.338,0.726-4.043-1.416-4.043-1.416   C4.421,18.069,3.635,17.7,3.635,17.7c-1.089-0.745,0.082-0.729,0.082-0.729c1.205,0.085,1.839,1.237,1.839,1.237   c1.07,1.834,2.807,1.304,3.492,0.997C9.156,18.429,9.467,17.9,9.81,17.6c-2.665-0.303-5.467-1.332-5.467-5.93   c0-1.31,0.469-2.381,1.237-3.221C5.455,8.146,5.044,6.926,5.696,5.273c0,0,1.008-0.322,3.301,1.23   C9.954,6.237,10.98,6.104,12,6.099c1.02,0.005,2.047,0.138,3.006,0.404c2.29-1.553,3.297-1.23,3.297-1.23   c0.653,1.653,0.242,2.873,0.118,3.176c0.769,0.84,1.235,1.911,1.235,3.221c0,4.609-2.807,5.624-5.479,5.921   c0.43,0.372,0.814,1.103,0.814,2.222c0,1.606-0.014,2.898-0.014,3.293c0,0.319,0.216,0.694,0.824,0.576   c4.766-1.589,8.2-6.085,8.2-11.385C24,5.669,18.627,0.296,12,0.296z"/>
                    <path d="M4.545,17.526c-0.026,0.06-0.12,0.078-0.206,0.037c-0.087-0.039-0.136-0.121-0.108-0.18   c0.026-0.061,0.12-0.078,0.207-0.037C4.525,17.384,4.575,17.466,4.545,17.526L4.545,17.526z"/>
                    <path d="M5.031,18.068c-0.057,0.053-0.169,0.028-0.245-0.055c-0.079-0.084-0.093-0.196-0.035-0.249   c0.059-0.053,0.167-0.028,0.246,0.056C5.076,17.903,5.091,18.014,5.031,18.068L5.031,18.068z"/>
                    <path d="M5.504,18.759c-0.074,0.051-0.194,0.003-0.268-0.103c-0.074-0.107-0.074-0.235,0.002-0.286   c0.074-0.051,0.193-0.005,0.268,0.101C5.579,18.579,5.579,18.707,5.504,18.759L5.504,18.759z"/>
                    <path d="M6.152,19.427c-0.066,0.073-0.206,0.053-0.308-0.046c-0.105-0.097-0.134-0.234-0.068-0.307   c0.067-0.073,0.208-0.052,0.311,0.046C6.191,19.217,6.222,19.355,6.152,19.427L6.152,19.427z"/>
                    <path d="M7.047,19.814c-0.029,0.094-0.164,0.137-0.3,0.097C6.611,19.87,6.522,19.76,6.55,19.665   c0.028-0.095,0.164-0.139,0.301-0.096C6.986,19.609,7.075,19.719,7.047,19.814L7.047,19.814z"/>
                    <path d="M8.029,19.886c0.003,0.099-0.112,0.181-0.255,0.183c-0.143,0.003-0.26-0.077-0.261-0.174c0-0.1,0.113-0.181,0.256-0.184   C7.912,19.708,8.029,19.788,8.029,19.886L8.029,19.886z"/>
                    <path d="M8.943,19.731c0.017,0.096-0.082,0.196-0.224,0.222c-0.139,0.026-0.268-0.034-0.286-0.13   c-0.017-0.099,0.084-0.198,0.223-0.224C8.797,19.574,8.925,19.632,8.943,19.731L8.943,19.731z"/>
                </g></svg>

        </button>
    </a>
</div>
<div class="px-6 ">
    <a href="https://t.me/wizwizch/119" target="_blank">
        <button
                class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-500 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue">
            Donate
            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" fill="#fff" viewBox="0 0 24 24"
                 width="15" height="15">
                <path d="M17.5.917a6.4,6.4,0,0,0-5.5,3.3A6.4,6.4,0,0,0,6.5.917,6.8,6.8,0,0,0,0,7.967c0,6.775,10.956,14.6,11.422,14.932l.578.409.578-.409C13.044,22.569,24,14.742,24,7.967A6.8,6.8,0,0,0,17.5.917Z"/>
            </svg>
        </button>
    </a>
</div>
</div>
</aside>
<!-- Mobile sidebar -->
<!-- Backdrop -->
<div
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-20 sm:items-center sm:justify-center"
></div>
<aside
        class="fixed inset-y-0 z-20 flex-shrink-0 w-64 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0 transform -translate-x-20"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform -translate-x-20"
        @click.away="closeSideMenu"
        @keydown.escape="closeSideMenu"
>
    <div class="py-4 text-gray-500 dark:text-gray-400">
            <div class="ml-2 flex justify-start items-center ">
            <img width="40px" src="./icons/wizwiz.png">
            <a class=" text-lg font-bold text-gray-800 dark:text-gray-200" href="index.php" > WizWiz <span class="px-1 ml-1 rounded" style="font-size: 10px;background-color: #e7cef1;color:#45013c !important;"> v 7.5.3</span></a>
            </div>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="index.php"
                >
                    <svg
                            class="w-5 h-5"
                            aria-hidden="true"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                    >
                        <path
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        ></path>
                    </svg>
                    <span class="ml-4"><?php echo $_LANG['Dashboard'] ?></span>
                </a>
            </li>
        </ul>
        <ul>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="orders.php"
                >
                    <svg
                            class="w-5 h-5"
                            aria-hidden="true"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                    >
                        <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        ></path>
                    </svg>
                    <span class="ml-4"><?php echo $_LANG['Orders'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="servers.php"
                >
                    <svg
                            class="w-5 h-5"
                            aria-hidden="true"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                    >
                        <path
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                        ></path>
                    </svg>
                    <span class="ml-4"><?php echo $_LANG['Servers'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="category.php"
                >
                    <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         id="Outline" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M7,0H4A4,4,0,0,0,0,4V7a4,4,0,0,0,4,4H7a4,4,0,0,0,4-4V4A4,4,0,0,0,7,0ZM9,7A2,2,0,0,1,7,9H4A2,2,0,0,1,2,7V4A2,2,0,0,1,4,2H7A2,2,0,0,1,9,4Z"/>
                        <path d="M7,13H4a4,4,0,0,0-4,4v3a4,4,0,0,0,4,4H7a4,4,0,0,0,4-4V17A4,4,0,0,0,7,13Zm2,7a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V17a2,2,0,0,1,2-2H7a2,2,0,0,1,2,2Z"/>
                        <path d="M20,13H17a4,4,0,0,0-4,4v3a4,4,0,0,0,4,4h3a4,4,0,0,0,4-4V17A4,4,0,0,0,20,13Zm2,7a2,2,0,0,1-2,2H17a2,2,0,0,1-2-2V17a2,2,0,0,1,2-2h3a2,2,0,0,1,2,2Z"/>
                        <path d="M14,7h3v3a1,1,0,0,0,2,0V7h3a1,1,0,0,0,0-2H19V2a1,1,0,0,0-2,0V5H14a1,1,0,0,0,0,2Z"/>
                    </svg>

                    <span class="ml-4"><?php echo $_LANG['category'] ?></span>
                </a>
            </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="singleplans.php"
        >
            <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 id="Outline" viewBox="0 0 24 24" width="20" height="20">
                <path d="M19,3H12.472a1.019,1.019,0,0,1-.447-.1L8.869,1.316A3.014,3.014,0,0,0,7.528,1H5A5.006,5.006,0,0,0,0,6V18a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V8A5.006,5.006,0,0,0,19,3ZM5,3H7.528a1.019,1.019,0,0,1,.447.1l3.156,1.579A3.014,3.014,0,0,0,12.472,5H19a3,3,0,0,1,2.779,1.882L2,6.994V6A3,3,0,0,1,5,3ZM19,21H5a3,3,0,0,1-3-3V8.994l20-.113V18A3,3,0,0,1,19,21Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['SinglePlans'] ?></span>
        </a>
    </li>
    <li class="relative px-6 py-3">
        <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="multipleplans.php"
        >
            <svg stroke-linejoin="round" fill="gray" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 id="Outline" viewBox="0 0 24 24" width="20" height="20">
                <path d="M19,3H12.472a1.019,1.019,0,0,1-.447-.1L8.869,1.316A3.014,3.014,0,0,0,7.528,1H5A5.006,5.006,0,0,0,0,6V18a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V8A5.006,5.006,0,0,0,19,3ZM5,3H7.528a1.019,1.019,0,0,1,.447.1l3.156,1.579A3.014,3.014,0,0,0,12.472,5H19a3,3,0,0,1,2.779,1.882L2,6.994V6A3,3,0,0,1,5,3ZM19,21H5a3,3,0,0,1-3-3V8.994l20-.113V18A3,3,0,0,1,19,21Z"/>
            </svg>

            <span class="ml-4"><?php echo $_LANG['MultiplePlans'] ?></span>
        </a>
    </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="pays.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" fill="gray" data-name="Layer 1"
                         viewBox="0 0 24 24" width="21" height="21">
                        <path d="M12,9C17.934,8.844,17.933,.155,12,0c-5.934,.156-5.933,8.845,0,9Zm0-7c3.286,.059,3.285,4.942,0,5-3.285-.059-3.285-4.942,0-5Zm10.204,9.162c-1.143-.953-2.64-1.347-4.099-1.081l-3.821,.695c-.913,.166-1.707,.634-2.284,1.289-.578-.655-1.371-1.123-2.285-1.289l-3.821-.695c-1.461-.264-2.956,.128-4.098,1.081-1.142,.953-1.796,2.352-1.796,3.839v2.793c0,2.417,1.727,4.486,4.106,4.919l6.284,1.143c1.068,.194,2.151,.194,3.219,0l6.285-1.143c2.379-.433,4.105-2.502,4.105-4.919v-2.793c0-1.487-.654-2.886-1.796-3.838Zm-11.204,10.767c-.084-.012-.168-.026-.252-.041l-6.284-1.143c-1.428-.26-2.464-1.501-2.464-2.952v-2.793c0-.892,.393-1.731,1.078-2.303,.685-.573,1.59-.808,2.459-.648l3.821,.695c.952,.173,1.642,1,1.642,1.968v7.217Zm11-4.135c0,1.451-1.036,2.692-2.463,2.952l-6.285,1.143c-.084,.015-.168,.029-.252,.041v-7.217c0-.967,.69-1.795,1.642-1.968l3.821-.695c.875-.16,1.774,.077,2.46,.648,.685,.572,1.077,1.411,1.077,2.303v2.793Z"/>
                    </svg>

                    <span class="ml-4"><?php echo $_LANG['Pays'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="add-volume.php">
                    <svg fill="gray" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512"
                         style="enable-background:new 0 0 512 512;" xml:space="preserve" width="21" height="21"><g>
                            <path d="M480,224H288V32c0-17.673-14.327-32-32-32s-32,14.327-32,32v192H32c-17.673,0-32,14.327-32,32s14.327,32,32,32h192v192   c0,17.673,14.327,32,32,32s32-14.327,32-32V288h192c17.673,0,32-14.327,32-32S497.673,224,480,224z"/>
                        </g></svg>


                    <span class="ml-4"><?php echo $_LANG['AddVolume'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="volume.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="gray" viewBox="0 0 24 24" width="21"
                         height="21">
                        <path d="M22.485,10.975,12,17.267,1.515,10.975A1,1,0,1,0,.486,12.69l11,6.6a1,1,0,0,0,1.03,0l11-6.6a1,1,0,1,0-1.029-1.715Z"/>
                        <path d="M22.485,15.543,12,21.834,1.515,15.543A1,1,0,1,0,.486,17.258l11,6.6a1,1,0,0,0,1.03,0l11-6.6a1,1,0,1,0-1.029-1.715Z"/>
                        <path d="M12,14.773a2.976,2.976,0,0,1-1.531-.425L.485,8.357a1,1,0,0,1,0-1.714L10.469.652a2.973,2.973,0,0,1,3.062,0l9.984,5.991a1,1,0,0,1,0,1.714l-9.984,5.991A2.976,2.976,0,0,1,12,14.773ZM2.944,7.5,11.5,12.633a.974.974,0,0,0,1,0L21.056,7.5,12.5,2.367a.974.974,0,0,0-1,0h0Z"/>
                    </svg>


                    <span class="ml-4"><?php echo $_LANG['Volumeorders'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="discount.php">
                    <svg id="Layer_1" height="21" viewBox="0 0 24 24" fill="gray" width="21"
                         xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m24 12a1 1 0 0 1 -2 0 10.011 10.011 0 0 0 -10-10 1 1 0 0 1 0-2 12.013 12.013 0 0 1 12 12zm-8 1a1 1 0 0 0 0-2h-2.277a2 2 0 0 0 -.723-.723v-3.277a1 1 0 0 0 -2 0v3.277a1.994 1.994 0 1 0 2.723 2.723zm-14.173-6.216a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm.173 5.216a1 1 0 1 0 -1 1 1 1 0 0 0 1-1zm10 10a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-7.779-18.793a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm3.558-2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-5.952 14.375a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm2.394 3.577a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm3.558 2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm14.394-5.943a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-2.394 3.577a1 1 0 1 0 1 1 1 1 0 0 0 -1-1zm-3.558 2.366a1 1 0 1 0 1 1 1 1 0 0 0 -1-1z"/>
                    </svg>

                    <span class="ml-4"><?php echo $_LANG['Discountcode'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="rahgozar.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" fill="gray"
                         viewBox="0 0 24 24" width="21" height="21">
                        <path d="M23,12.5A1.5,1.5,0,0,1,21.5,14H18.63a3.516,3.516,0,0,1-3-1.7l-1.225-2.034-1.79,4.363,2.614,1.487A3.507,3.507,0,0,1,17,19.163V22.5a1.5,1.5,0,0,1-3,0V19.163a.5.5,0,0,0-.252-.434L9.666,16.406a3.511,3.511,0,0,1-1.427-4.322L9.5,9H7.736a.5.5,0,0,0-.447.277L5.842,12.171a1.5,1.5,0,0,1-2.684-1.342L4.605,7.935A3.483,3.483,0,0,1,7.736,6H13.36a3.516,3.516,0,0,1,3,1.7L18.2,10.758A.5.5,0,0,0,18.63,11H21.5A1.5,1.5,0,0,1,23,12.5ZM8.057,16.85a1.5,1.5,0,0,0-1.95.836A.5.5,0,0,1,5.643,18H3.5a1.5,1.5,0,0,0,0,3H5.643a3.484,3.484,0,0,0,3.25-2.2A1.5,1.5,0,0,0,8.057,16.85ZM14.5,5A2.5,2.5,0,1,0,12,2.5,2.5,2.5,0,0,0,14.5,5Z"/>
                    </svg>


                    <span class="ml-4"><?php echo $_LANG['Rahgozar'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="gift.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" fill="gray" data-name="Layer 1"
                         viewBox="0 0 24 24" width="21" height="21">
                        <path d="M21,7H17.866A6.547,6.547,0,0,0,20,2H18c0,2.881-1.971,4.307-4.152,4.8A9.239,9.239,0,0,0,15,3,3,3,0,0,0,9,3a9.239,9.239,0,0,0,1.152,3.8C7.971,6.307,6,4.881,6,2H4A6.547,6.547,0,0,0,6.134,7H3a3,3,0,0,0-3,3v4H2V24H22V14h2V10A3,3,0,0,0,21,7ZM12,2a1,1,0,0,1,1,1,7.71,7.71,0,0,1-1,3.013A7.71,7.71,0,0,1,11,3,1,1,0,0,1,12,2ZM2,10A1,1,0,0,1,3,9h8v3H2Zm2,4h7v8H4Zm16,8H13V14h7Zm2-10H13V9h8a1,1,0,0,1,1,1Z"/>
                    </svg>


                    <span class="ml-4"><?php echo $_LANG['Gift'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
              <span
                      class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"
              ></span>
                <a
                        class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                        href="software.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24"
                         width="21" height="21">
                        <path d="M11.24,24a2.262,2.262,0,0,1-.948-.212,2.18,2.18,0,0,1-1.2-2.622L10.653,16H6.975A3,3,0,0,1,4.1,12.131l3.024-10A2.983,2.983,0,0,1,10,0h3.693a2.6,2.6,0,0,1,2.433,3.511L14.443,8H17a3,3,0,0,1,2.483,4.684l-6.4,10.3A2.2,2.2,0,0,1,11.24,24ZM10,2a1,1,0,0,0-.958.71l-3.024,10A1,1,0,0,0,6.975,14H12a1,1,0,0,1,.957,1.29L11.01,21.732a.183.183,0,0,0,.121.241A.188.188,0,0,0,11.4,21.9l6.4-10.3a1,1,0,0,0,.078-1.063A.979.979,0,0,0,17,10H13a1,1,0,0,1-.937-1.351l2.19-5.84A.6.6,0,0,0,13.693,2Z"/>
                    </svg>
                    <span class="ml-4"><?php echo $_LANG['Software'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="wizwizbackup.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="gray" id="Outline" viewBox="0 0 24 24" width="21" height="21"><path d="M18.4,7.379a1.128,1.128,0,0,1-.769-.754h0a8,8,0,1,0-15.1,5.237A1.046,1.046,0,0,1,2.223,13.1,5.5,5.5,0,0,0,.057,18.3,5.622,5.622,0,0,0,5.683,23H11a1,1,0,0,0,1-1h0a1,1,0,0,0-1-1H5.683a3.614,3.614,0,0,1-3.646-2.981,3.456,3.456,0,0,1,1.376-3.313A3.021,3.021,0,0,0,4.4,11.141a6.113,6.113,0,0,1-.073-4.126A5.956,5.956,0,0,1,9.215,3.05,6.109,6.109,0,0,1,9.987,3a5.984,5.984,0,0,1,5.756,4.28,2.977,2.977,0,0,0,2.01,1.99,5.934,5.934,0,0,1,.778,11.09.976.976,0,0,0-.531.888h0a.988.988,0,0,0,1.388.915c4.134-1.987,6.38-7.214,2.88-12.264A6.935,6.935,0,0,0,18.4,7.379Z"/><path d="M18.707,16.707a1,1,0,0,0,0-1.414l-1.586-1.586a3,3,0,0,0-4.242,0l-1.586,1.586a1,1,0,0,0,1.414,1.414L14,15.414V23a1,1,0,0,0,2,0V15.414l1.293,1.293a1,1,0,0,0,1.414,0Z"/></svg>
                    <span class="ml-4"><?php echo $_LANG['Backup'] ?></span>
                </a>
            </li>
            <li class="relative px-6 py-3">
                <a
                        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                        href="settings.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="gray" id="Outline" viewBox="0 0 24 24" width="21"
                         height="21">
                        <path d="M12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z"/>
                        <path d="M21.294,13.9l-.444-.256a9.1,9.1,0,0,0,0-3.29l.444-.256a3,3,0,1,0-3-5.2l-.445.257A8.977,8.977,0,0,0,15,3.513V3A3,3,0,0,0,9,3v.513A8.977,8.977,0,0,0,6.152,5.159L5.705,4.9a3,3,0,0,0-3,5.2l.444.256a9.1,9.1,0,0,0,0,3.29l-.444.256a3,3,0,1,0,3,5.2l.445-.257A8.977,8.977,0,0,0,9,20.487V21a3,3,0,0,0,6,0v-.513a8.977,8.977,0,0,0,2.848-1.646l.447.258a3,3,0,0,0,3-5.2Zm-2.548-3.776a7.048,7.048,0,0,1,0,3.75,1,1,0,0,0,.464,1.133l1.084.626a1,1,0,0,1-1,1.733l-1.086-.628a1,1,0,0,0-1.215.165,6.984,6.984,0,0,1-3.243,1.875,1,1,0,0,0-.751.969V21a1,1,0,0,1-2,0V19.748a1,1,0,0,0-.751-.969A6.984,6.984,0,0,1,7.006,16.9a1,1,0,0,0-1.215-.165l-1.084.627a1,1,0,1,1-1-1.732l1.084-.626a1,1,0,0,0,.464-1.133,7.048,7.048,0,0,1,0-3.75A1,1,0,0,0,4.79,8.992L3.706,8.366a1,1,0,0,1,1-1.733l1.086.628A1,1,0,0,0,7.006,7.1a6.984,6.984,0,0,1,3.243-1.875A1,1,0,0,0,11,4.252V3a1,1,0,0,1,2,0V4.252a1,1,0,0,0,.751.969A6.984,6.984,0,0,1,16.994,7.1a1,1,0,0,0,1.215.165l1.084-.627a1,1,0,1,1,1,1.732l-1.084.626A1,1,0,0,0,18.746,10.125Z"/>
                    </svg>

                    <span class="ml-4"><?php echo $_LANG['Settings'] ?></span>
                </a>
            </li>
        </ul>
        <div class="px-6 mt-3">
            <a href="https://t.me/wizwizch" target="_blank">
                <button class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                    Telegram wizwiz
                    <span class="ml-2" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                               fill="#fff" width="15" height="15"><g
                                    id="_01_align_center" data-name="01 align center"><path
                                        d="M1.444,6.669a2,2,0,0,0-.865,3.337l3.412,3.408V20h6.593l3.435,3.43a1.987,1.987,0,0,0,1.408.588,2.034,2.034,0,0,0,.51-.066,1.978,1.978,0,0,0,1.42-1.379L23.991.021ZM2,8.592l17.028-5.02L5.993,16.586v-4Zm13.44,13.424L11.413,18h-4L20.446,4.978Z"/></g></svg></span>
                </button>
            </a>
        </div>
        <div class="px-6 my-2">
            <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank">
                <button class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-500 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                    Github
                    <span class="ml-2" aria-hidden="true">
            <svg fill="#fff" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve" width="15" height="15"><g>
                    <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M12,0.296c-6.627,0-12,5.372-12,12c0,5.302,3.438,9.8,8.206,11.387   c0.6,0.111,0.82-0.26,0.82-0.577c0-0.286-0.011-1.231-0.016-2.234c-3.338,0.726-4.043-1.416-4.043-1.416   C4.421,18.069,3.635,17.7,3.635,17.7c-1.089-0.745,0.082-0.729,0.082-0.729c1.205,0.085,1.839,1.237,1.839,1.237   c1.07,1.834,2.807,1.304,3.492,0.997C9.156,18.429,9.467,17.9,9.81,17.6c-2.665-0.303-5.467-1.332-5.467-5.93   c0-1.31,0.469-2.381,1.237-3.221C5.455,8.146,5.044,6.926,5.696,5.273c0,0,1.008-0.322,3.301,1.23   C9.954,6.237,10.98,6.104,12,6.099c1.02,0.005,2.047,0.138,3.006,0.404c2.29-1.553,3.297-1.23,3.297-1.23   c0.653,1.653,0.242,2.873,0.118,3.176c0.769,0.84,1.235,1.911,1.235,3.221c0,4.609-2.807,5.624-5.479,5.921   c0.43,0.372,0.814,1.103,0.814,2.222c0,1.606-0.014,2.898-0.014,3.293c0,0.319,0.216,0.694,0.824,0.576   c4.766-1.589,8.2-6.085,8.2-11.385C24,5.669,18.627,0.296,12,0.296z"/>
                    <path d="M4.545,17.526c-0.026,0.06-0.12,0.078-0.206,0.037c-0.087-0.039-0.136-0.121-0.108-0.18   c0.026-0.061,0.12-0.078,0.207-0.037C4.525,17.384,4.575,17.466,4.545,17.526L4.545,17.526z"/>
                    <path d="M5.031,18.068c-0.057,0.053-0.169,0.028-0.245-0.055c-0.079-0.084-0.093-0.196-0.035-0.249   c0.059-0.053,0.167-0.028,0.246,0.056C5.076,17.903,5.091,18.014,5.031,18.068L5.031,18.068z"/>
                    <path d="M5.504,18.759c-0.074,0.051-0.194,0.003-0.268-0.103c-0.074-0.107-0.074-0.235,0.002-0.286   c0.074-0.051,0.193-0.005,0.268,0.101C5.579,18.579,5.579,18.707,5.504,18.759L5.504,18.759z"/>
                    <path d="M6.152,19.427c-0.066,0.073-0.206,0.053-0.308-0.046c-0.105-0.097-0.134-0.234-0.068-0.307   c0.067-0.073,0.208-0.052,0.311,0.046C6.191,19.217,6.222,19.355,6.152,19.427L6.152,19.427z"/>
                    <path d="M7.047,19.814c-0.029,0.094-0.164,0.137-0.3,0.097C6.611,19.87,6.522,19.76,6.55,19.665   c0.028-0.095,0.164-0.139,0.301-0.096C6.986,19.609,7.075,19.719,7.047,19.814L7.047,19.814z"/>
                    <path d="M8.029,19.886c0.003,0.099-0.112,0.181-0.255,0.183c-0.143,0.003-0.26-0.077-0.261-0.174c0-0.1,0.113-0.181,0.256-0.184   C7.912,19.708,8.029,19.788,8.029,19.886L8.029,19.886z"/>
                    <path d="M8.943,19.731c0.017,0.096-0.082,0.196-0.224,0.222c-0.139,0.026-0.268-0.034-0.286-0.13   c-0.017-0.099,0.084-0.198,0.223-0.224C8.797,19.574,8.925,19.632,8.943,19.731L8.943,19.731z"/>
                </g></svg>
                </button>
            </a>
        </div>
        <div class="px-6 my-2">
            <a href="https://t.me/wizwizch/119" target="_blank">
                <button class="flex items-center shadow-xl justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-500 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-blue">
                    Donate
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" fill="#fff"
                         viewBox="0 0 24 24" width="15" height="15">
                        <path d="M17.5.917a6.4,6.4,0,0,0-5.5,3.3A6.4,6.4,0,0,0,6.5.917,6.8,6.8,0,0,0,0,7.967c0,6.775,10.956,14.6,11.422,14.932l.578.409.578-.409C13.044,22.569,24,14.742,24,7.967A6.8,6.8,0,0,0,17.5.917Z"/>
                    </svg>
                </button>
            </a>
        </div>
    </div>
</aside>
<div class="flex flex-col flex-1 w-full">
    <?php
    include 'includ/top-header.php';
    ?>
    <main class="h-full pb-16 overflow-y-auto">
        <div class="container grid px-6 mx-auto ">


            <a style="font-size: 20px"
               class="text-xs font-semibold tracking-wide text-left text-gray-500 0 dark:text-gray-400 inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
            >
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" fill="gray" viewBox="0 0 24 24"
                     width="21" height="21">
                    <path d="M11.24,24a2.262,2.262,0,0,1-.948-.212,2.18,2.18,0,0,1-1.2-2.622L10.653,16H6.975A3,3,0,0,1,4.1,12.131l3.024-10A2.983,2.983,0,0,1,10,0h3.693a2.6,2.6,0,0,1,2.433,3.511L14.443,8H17a3,3,0,0,1,2.483,4.684l-6.4,10.3A2.2,2.2,0,0,1,11.24,24ZM10,2a1,1,0,0,0-.958.71l-3.024,10A1,1,0,0,0,6.975,14H12a1,1,0,0,1,.957,1.29L11.01,21.732a.183.183,0,0,0,.121.241A.188.188,0,0,0,11.4,21.9l6.4-10.3a1,1,0,0,0,.078-1.063A.979.979,0,0,0,17,10H13a1,1,0,0,1-.937-1.351l2.19-5.84A.6.6,0,0,0,13.693,2Z"/>
                </svg>
                <span class="ml-4"><?php echo $_LANG['software']?></span>
                <span class="ml-4 justify-end inline-flex">
                    <button @click="openModal"
                            class=" text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent rounded-lg focus:outline-none ">
                        <img src="icons/add.svg" width="30px">
                    </button>
                    </span>
                <?php session_notif_wizwiz() ?>
            </a>

            <div style='margin-top:40px' class=" w-full flex justify-center items-center grid gap-6 mb-8 xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-2 grid-cols-2">
                <?php
                software($conn);
                $software_select = software_select($conn);
                if (is_array($software_select) || is_object($software_select)) {
                    foreach ($software_select
                             as $value):
                        echo '
                    


<div class="w-full p-4 m-auto bg-white shadow-md rounded-2xl dark:bg-gray-800 flex items-center justify-center">
    <div class="w-full h-full text-center">
        <div class="flex flex-col justify-center h-full">
            <img src="icons/plans.svg" width="50px" class="mx-auto mt-3" style="color: #5f7d95">
            <p class="mt-4 text-xl font-bold text-gray-800 dark:text-gray-200" style="font-size: 16px">
                ' . $value['title'] . '
            </p>

            <div class="flex items-center justify-center w-full gap-4 mt-4">'
                        ?>
                        <?php
                        if ($value['status'] == '1') {
                            echo '<a href="software.php?off=' . $value['id'] . '">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="m-2" width="27" fill="#a1c181"><path d="M23.821,11.181v0C22.943,9.261,19.5,3,12,3S1.057,9.261.179,11.181a1.969,1.969,0,0,0,0,1.64C1.057,14.739,4.5,21,12,21s10.943-6.261,11.821-8.181A1.968,1.968,0,0,0,23.821,11.181ZM12,18a6,6,0,1,1,6-6A6.006,6.006,0,0,1,12,18Z"/><circle cx="12" cy="12" r="4"/></svg>
                    </a>';
                        } else {
                            echo '<a href="software.php?on=' . $value['id'] . '">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="m-2" width="27" fill="#6c757d"><path d="M23.821,11.181v0C22.943,9.261,19.5,3,12,3S1.057,9.261.179,11.181a1.969,1.969,0,0,0,0,1.64C1.057,14.739,4.5,21,12,21s10.943-6.261,11.821-8.181A1.968,1.968,0,0,0,23.821,11.181ZM12,18a6,6,0,1,1,6-6A6.006,6.006,0,0,1,12,18Z"/><circle cx="12" cy="12" r="4"/></svg>
                    </a>';
                        }
                        ?>
                        <?php
                        echo '<a href="software.php?delete=' . $value['id'] . '">
                    <svg class="m-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" fill="#ff758f"><g id="_01_align_center" data-name="01 align center"><path d="M18.965,8.464l5.029-5.029L22.565,2.006,17.519,7.051,15.313,4.895a2.011,2.011,0,0,0-2.724-.073A19.71,19.71,0,0,1,6.226,8.687L-.006,10.514v.756A12.892,12.892,0,0,0,13.006,24.006h1.241l.261-.182a14.98,14.98,0,0,0,6.886-12.081,2.013,2.013,0,0,0-.611-1.5ZM13.9,6.34l5.477,5.341a12.652,12.652,0,0,1-.456,3.015L11.941,7.9A24.691,24.691,0,0,0,13.9,6.34Zm-.294,15.645h-.6a11.153,11.153,0,0,1-4.293-.859,11.55,11.55,0,0,0,4.348-3.8l.518-.76L11.91,15.43l-.518.761A9.539,9.539,0,0,1,7.8,19.324l-1.229.618a10.9,10.9,0,0,1-2.585-2.563,11.632,11.632,0,0,0,4.357-2.657L6.932,13.277a9.611,9.611,0,0,1-3.974,2.317,10.4,10.4,0,0,1-.921-3.575l4.755-1.394a15.234,15.234,0,0,0,3.423-1.59l7.911,7.7A14.1,14.1,0,0,1,13.608,21.985Z"/></g></svg>
                    </a>
            </div>
        </div>
    </div>
</div>



               
                    ';
                    endforeach;
                }
                ?>
            </div>
            <!--                </div>-->

        </div>
    </main>
</div>
</div>


<!-- Modal backdrop. This what you want to place close to the closing body tag -->
<div
        x-show="isModalOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-30 sm:items-center sm:justify-center"
>
    <!-- Modal -->
    <div
            x-show="isModalOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform translate-y-1/2"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0  transform translate-y-1/2"
            @click.away="closeModal"
            @keydown.escape="closeModal"
            class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl"
            role="dialog"
            id="modal"
    >
        <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
        <header class="flex justify-end">
        </header>
        <?php
        software_insert($conn);
        ?>
        <form id="insert_software">
            <!-- Modal body -->
            <div class="">
                <p class="m-4" style="color: #710000;font-size: 15px">
                    <?php echo $_LANG['addsoftware']?>
                <div class="flex relative m-2">
                    <span class="dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray rounded-l-md inline-flex  items-center px-3 border-t bg-white border-l border-b  border-gray-300 text-gray-500 shadow-sm text-sm">
                        <?php echo $_LANG['title']?>
                    </span>
                    <input name="title_software" style="font-size: 14px;" value="" type="text" id="with-email"
                           class="dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-red-400 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-transparent"
                           placeholder="" required/>
                </div>
                <div class="flex relative m-2">
                    <span class="dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray rounded-l-md inline-flex  items-center px-3 border-t bg-white border-l border-b  border-gray-300 text-gray-500 shadow-sm text-sm">
                        <?php echo $_LANG['url']?>
                    </span>
                    <input name="url_software" style="font-size: 14px;" value="" type="text" id="with-email"
                           class="dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-red-400 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-transparent"
                           placeholder="" required/>
                </div>
                <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800">
                    <button type="button" style="border: 1px solid #e76d81"
                            class="w-full inline-flex items-center justify-center px-5 py-3 transition-colors duration-150 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 hover:bg-red-100 "
                            aria-label="close" @click="closeModal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#e76d81" id="Outline" viewBox="0 0 24 24"
                             width="21"
                             height="21">
                            <path d="M23.707.293h0a1,1,0,0,0-1.414,0L12,10.586,1.707.293a1,1,0,0,0-1.414,0h0a1,1,0,0,0,0,1.414L10.586,12,.293,22.293a1,1,0,0,0,0,1.414h0a1,1,0,0,0,1.414,0L12,13.414,22.293,23.707a1,1,0,0,0,1.414,0h0a1,1,0,0,0,0-1.414L13.414,12,23.707,1.707A1,1,0,0,0,23.707.293Z"/>
                        </svg>
                    </button>
                    <button style="border: 1px solid #41c76e" name="add_software" type="submit" @click="closeModal"
                            class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-15 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 hover:bg-green-100 ">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="#41c76e" viewBox="0 0 24 24"
                             width="22"
                             height="22">
                            <path d="M22.319,4.431,8.5,18.249a1,1,0,0,1-1.417,0L1.739,12.9a1,1,0,0,0-1.417,0h0a1,1,0,0,0,0,1.417l5.346,5.345a3.008,3.008,0,0,0,4.25,0L23.736,5.847a1,1,0,0,0,0-1.416h0A1,1,0,0,0,22.319,4.431Z"/>
                        </svg>
                    </button>
                </footer>
        </form>
    </div>
</div>

<?php
include 'includ/footer.php';
?>
