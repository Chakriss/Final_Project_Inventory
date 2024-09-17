<style>
    .carousel-inner img {
        height: 500px; /* คุณสามารถปรับขนาดนี้ตามต้องการ */
        object-fit: cover;
    }
</style>

<div id="main">

    <div class="page-heading">
        <h3>Welcome To Inventory Management System</h3>
    </div>
    <div class="page-content">
        <section class="row">
            
            <div class="col-12 col-lg-9">
                <div class="card">

                    <div class="card-body">
                        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                                    class="active"></li>
                                <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></li>
                                <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="assets/images/samples/stock1.jpg" class="d-block w-100" alt="...">
                                    <div class="carousel-caption d-none d-md-block">

                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="assets/images/samples/stock4.jpg" class="d-block w-100" alt="...">
                                    <div class="carousel-caption d-none d-md-block">
                                       
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="assets/images/samples/stock3.jpg" class="d-block w-100" alt="...">
                                    <div class="carousel-caption d-none d-md-block">

                                    </div>
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleCaptions" role="button"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-5">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="assets/images/faces/2.jpg" alt="Face 1">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">Hello</h5>
                                <h6 class="text-muted mb-0">@<?php echo $_SESSION["user_name"] ?></h6>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </section>
    </div>