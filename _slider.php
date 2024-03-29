  <section class="flexslider std-slider" data-height="740" data-loop="true" data-smooth="false" data-slideshow="true" data-speed="15000" data-animspeed="550" data-controls="true" data-dircontrols="true">
  <ul class="slides">
    <li data-bg="/images/content/home-v1-slider-03.webp" class="img-fluid" style="background-position: top; background-size: cover; min-height: 740px !important;">
      <div class="container">
        <div class="inner">
          <div class="row">
            <div class="col-md-6 pull-left animated" data-fx="fadeIn" style="width: 100%; text-align: center; margin-top:0;">
              

              <h3 class="pa slidertext" style="text-align:center; color: #fff; line-height: 40px !important; text-shadow: -4px 4px 4px black !important; -webkit-text-stroke-width:0px; max-width: 625px;">
                <a href="/seance-decouverte/Antibes">
                  <span class="bigger" style="color: #f2e4b3; font-size: 135%; line-height: 40px !important;"></span>
                  <br />
                  <br />
                  <br />
                  <br />
                  <br />
                  <br /><span class="bigger" style="color: #e51cb3; font-size: 100%; line-height: 40px !important;"></span>
                  <br>
                  <br /> <span class="bigger" style="color: #ffffff; font-size: 100%; line-height: 40px !important;">Vitalité, Minceur,</span>
                  <br/>
                  <br/><span class="bigger" style="color: #ffffff; font-size:100%; line-height: 40px !important; ">et Bien-être</span>
                </a>
              </h3>

              <!-- commentaire 
              <a href="/seance-decouverte/Cannes" id="sticker-text" style="float:left; clear: both;  background-image: url(/images/sticker1-orange.png); background-repeat: no-repeat; background-size: cover; display: inline-block;  padding: 4.5rem 5rem 2rem; text-decoration:none; margin-top: 100px;">
                <p class="media-heading">CLIQUEZ POUR<br>ESSAYER<br> UNE SÉANCE<br>GRATUITE<br>DE 45 MN</p>
              </a> 
              -->
            </div>
          </div>
        </div>
      </div>
    </li>
    <li data-bg="/images/content/home-v1-slider-01.jpg">
      <div class="container">
        <div class="inner">
          <div class="row">
            <div class="col-md-6 animated" data-fx="fadeIn">
              <h2>Atténuer la cellulite</h2>
              <p>Saviez-vous qu'une 1/2 heure d'aquabiking équivaut à une heure de fitness avec 3 fois plus de calories brûlées ?</p>
              <a href="/aquabiking" class="btn btn-default btn-lg">Les bienfaits</a> 
            </div>
            <div class="col-md-6"></div>
          </div>
        </div>
      </div>
    </li>
    <li data-bg="/images/content/home-v2-slider-01.jpg">
      <div class="container">
        <div class="inner">
          <div class="text-center animated" data-fx="fadeIn">
            <h2 class="page-title">D&eacute;couvrez</h2>
            <h2>LE NOUVEL AQUAVELO</h2>
          </div>
        </div>
      </div>
    </li>
  </ul>
</section>
   <br />

<!-- Formulaire -->
              <h2 class="form-group" style="text-align: center;">Essayez une séance gratuite de 45 mn</h2>
              <div class="col-md-6">
                <form role="form" class="contact-form" method="POST" action="_page.php">
                  <div class="form-group">
                    <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?</label>
                    <select class="form-control" id="center" name="center">
                      <?php foreach ($centers_list_d as &$free_d) { ?>
                        <option <?php if (isset($_GET['city']) &&  $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= $free_d['id'] ?>"><?= $free_d['city'] ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="nom">Nom et prénom</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom et prénom">
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                  </div>
                  <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="phone" class="form-control" id="phone" name="phone" placeholder="Téléphone">
                  </div>
                  <input type="hidden" name="reason" id="reason">
                  <input type="hidden" name="segment" id="segment">
                  <button type="submit" class="btn btn-default">Recevoir mon bon par email</button>
                </form>
              </div>
              <!-- Fin du formulaire -->

