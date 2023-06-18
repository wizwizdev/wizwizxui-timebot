<!--
* WizWiz v7.5.3
* https://github.com/wizwizdev/wizwizxui-timebot
* Copyright (c) @wizwizch
-->
<header class="">
    <div style="margin: 2%;"
         class="container flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300"
    >
        <!-- Mobile hamburger -->
        <button
                class=" pb-10 md:hidden focus:outline-none "
                @click="toggleSideMenu"
                aria-label="Menu"
        >
            <svg
                    class="w-10 h-10"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 20 20"
            >
                <path
                        fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"
                ></path>
            </svg>
        </button>
        <!-- Search input -->
        <div style="position: fixed; top: 3%;right: 5%;z-index: 1000;padding: 5px">
            <ul class="flex flex-shrink-0 space-x-1">
                <li class="relative flex items-center">
                    <p>
                        <a href="https://github.com/wizwizdev/wizwizxui-timebot">
                            <img width="85px" alt="GitHub wizwiz stars"
                                 src="https://img.shields.io/github/stars/wizwizdev/wizwizxui-timebot?color=%23f3f4f8&label=star">
                        </a>
                    </p>
                </li>
                <li class="flex">
                    <button
                            class="rounded-md focus:outline-none focus:shadow-outline-purple border-2 p-1 dark:border-gray-700 "
                            @click="toggleTheme"
                            aria-label="Toggle color mode"
                    >
                        <template x-if="!dark">
                            <svg
                                    class="w-5 h-5"
                                    aria-hidden="true"
                                    fill="gray"
                                    viewBox="0 0 20 20"
                            >
                                <path
                                        d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                                ></path>
                            </svg>
                        </template>
                        <template x-if="dark">
                            <svg
                                    class="w-5 h-5"
                                    aria-hidden="true"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                            >
                                <path
                                        fill-rule="evenodd"
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        clip-rule="evenodd"
                                ></path>
                            </svg>
                        </template>
                    </button>
                </li>
                <li class="relative">
                    <a href="port/index.php" target="_blank">
                        <button
                                class="align-middle  focus:shadow-outline-purple rounded-md focus:outline-none border-2 p-1 dark:border-gray-700"
                                @click="toggleProfileMenu"
                                @keydown.escape="closeProfileMenu"
                                aria-label="Account"
                                aria-haspopup="true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="gray" id="Outline" viewBox="0 0 24 24" width="20" height="20"><path d="M17,8H7V7a5,5,0,0,1,9.375-2.422,1,1,0,0,0,1.749-.971A7,7,0,0,0,5,7V8.424A5,5,0,0,0,2,13v6a5.006,5.006,0,0,0,5,5H17a5.006,5.006,0,0,0,5-5V13A5.006,5.006,0,0,0,17,8Zm3,11a3,3,0,0,1-3,3H7a3,3,0,0,1-3-3V13a3,3,0,0,1,3-3H17a3,3,0,0,1,3,3Z"/><path d="M12,14a1,1,0,0,0-1,1v2a1,1,0,0,0,2,0V15A1,1,0,0,0,12,14Z"/></svg>
                        </button>
                    </a>
                </li>
                <?php
                $sql_admins = "SELECT * FROM admins";
                $result_admins = $conn->query($sql_admins);
                $row_admins = $result_admins->fetch_assoc();

                if (isset($_GET['off'])) {
                    $user_lang = $_GET['off'];
                    $sql_on_select = "UPDATE admins SET lang='fa' WHERE lang='$user_lang'";
                    $res_on_select = mysqli_query($conn, $sql_on_select);
                    if (!$res_on_select) {
                        echo "خطا" . die(mysqli_error($conn));
                    } else {
                        statusonwizwiz();
                        header("location: index.php");
                    }
                }

                if (isset($_GET['on'])) {
                    $user_lang1 = $_GET['on'];
                    $sql_off_select = "UPDATE admins SET lang='en' WHERE lang='$user_lang1'";
                    $res_off_select = mysqli_query($conn, $sql_off_select);
                    if (!$res_off_select) {
                        echo "خطا" . die(mysqli_error($conn));
                    } else {
                        statusoffwizwiz();
                        header("location: index.php");
                    }
                }
                if ($row_admins["lang"] == 'en') {
                    echo '<li class="relative"><a href="index.php?off=' . $row_admins["lang"] . '" >
                        <button
                                class="align-middle  focus:shadow-outline-purple rounded-md focus:outline-none border-2 p-1 dark:border-gray-700"
                                @click="toggleProfileMenu"
                                @keydown.escape="closeProfileMenu"
                                aria-label="Account"
                                aria-haspopup="true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="gray" viewBox="0 0 24 24" width="20" height="20"><path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0ZM22,12a9.938,9.938,0,0,1-1.662,5.508l-1.192-1.193A.5.5,0,0,1,19,15.962V15a3,3,0,0,0-3-3H13a1,1,0,0,1-1-1v-.5a.5.5,0,0,1,.5-.5A2.5,2.5,0,0,0,15,7.5v-1a.5.5,0,0,1,.5-.5h1.379a2.516,2.516,0,0,0,1.767-.732l.377-.377A9.969,9.969,0,0,1,22,12Zm-19.951.963,3.158,3.158A2.978,2.978,0,0,0,7.329,17H10a1,1,0,0,1,1,1v3.949A10.016,10.016,0,0,1,2.049,12.963ZM13,21.949V18a3,3,0,0,0-3-3H7.329a1,1,0,0,1-.708-.293L2.163,10.249A9.978,9.978,0,0,1,17.456,3.63l-.224.224A.507.507,0,0,1,16.879,4H15.5A2.5,2.5,0,0,0,13,6.5v1a.5.5,0,0,1-.5.5A2.5,2.5,0,0,0,10,10.5V11a3,3,0,0,0,3,3h3a1,1,0,0,1,1,1v.962a2.516,2.516,0,0,0,.732,1.767l1.337,1.337A9.971,9.971,0,0,1,13,21.949Z"/></svg>
                        </button>
                        </a></li>';
                } else {
                    echo '<li class="relative"><a href="index.php?on=' . $row_admins["lang"] . '" >
                        <button
                                class="align-middle  focus:shadow-outline-purple rounded-md focus:outline-none border-2 p-1 dark:border-gray-700"
                                @click="toggleProfileMenu"
                                @keydown.escape="closeProfileMenu"
                                aria-label="Account"
                                aria-haspopup="true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" id="Outline" fill="gray" viewBox="0 0 24 24" width="20" height="20"><path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0ZM22,12a9.938,9.938,0,0,1-1.662,5.508l-1.192-1.193A.5.5,0,0,1,19,15.962V15a3,3,0,0,0-3-3H13a1,1,0,0,1-1-1v-.5a.5.5,0,0,1,.5-.5A2.5,2.5,0,0,0,15,7.5v-1a.5.5,0,0,1,.5-.5h1.379a2.516,2.516,0,0,0,1.767-.732l.377-.377A9.969,9.969,0,0,1,22,12Zm-19.951.963,3.158,3.158A2.978,2.978,0,0,0,7.329,17H10a1,1,0,0,1,1,1v3.949A10.016,10.016,0,0,1,2.049,12.963ZM13,21.949V18a3,3,0,0,0-3-3H7.329a1,1,0,0,1-.708-.293L2.163,10.249A9.978,9.978,0,0,1,17.456,3.63l-.224.224A.507.507,0,0,1,16.879,4H15.5A2.5,2.5,0,0,0,13,6.5v1a.5.5,0,0,1-.5.5A2.5,2.5,0,0,0,10,10.5V11a3,3,0,0,0,3,3h3a1,1,0,0,1,1,1v.962a2.516,2.516,0,0,0,.732,1.767l1.337,1.337A9.971,9.971,0,0,1,13,21.949Z"/></svg>
                        </button>
                        </a></li>';
                }
                ?>
                <li class="relative">
                    <a href="exit.php">
                        <button
                                class="align-middle focus:shadow-outline-purple rounded-md focus:outline-none border-2 p-1 dark:border-gray-700"
                                @click="toggleProfileMenu"
                                @keydown.escape="closeProfileMenu"
                                aria-label="Account"
                                aria-haspopup="true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode"
                                 fill="gray" viewBox="0 0 24 24" width="20" height="20">
                                <path d="M16,2.764V6.082a8,8,0,1,1-8,0V2.764a11,11,0,1,0,8,0Z"/>
                                <rect x="10.5" width="3" height="8"/>
                            </svg>

                        </button>
                    </a>

                </li>
            </ul>
        </div>
    </div>

</header>