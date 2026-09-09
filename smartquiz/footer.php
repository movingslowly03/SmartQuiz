    </main>

</div>

<footer>

    <p>
        &copy; <?php echo date("Y"); ?> SmartQuiz.
        All Rights Reserved.
    </p>

</footer>

<script>

/* ==========================
   Mobile Sidebar
========================== */

document.addEventListener("DOMContentLoaded", function(){

    const menu = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if(menu && sidebar && overlay){

        menu.addEventListener("click", function(){

            sidebar.classList.toggle("show");
            overlay.classList.toggle("show");

        });

        overlay.addEventListener("click", function(){

            sidebar.classList.remove("show");
            overlay.classList.remove("show");

        });

        sidebar.querySelectorAll("a").forEach(function(link){

            link.addEventListener("click", function(){

                if(window.innerWidth <= 768){
                    sidebar.classList.remove("show");
                    overlay.classList.remove("show");
                }

            });

        });

    }

});

/* ==========================
   Service Worker
========================== */

if("serviceWorker" in navigator)
{
    window.addEventListener("load", function(){

        navigator.serviceWorker
            .register("/smartquiz/service-worker.js")
            .then(reg => console.log("Service Worker registered:", reg.scope))
            .catch(err => console.error("Service Worker failed:", err));

    });
}

</script>

</body>

</html>
