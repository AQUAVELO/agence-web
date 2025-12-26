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
              <h2>Dites adieu à la cellulite</h2>
              <p>30 min d'aquabiking = 1h de fitness · <strong>Brûlez 500 calories par séance</strong> grâce à la résistance de l'eau !</p>
              <a href="/?p=free" class="btn btn-primary btn-lg"><i class="fa fa-gift"></i> Essai gratuit</a> 
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
            <p style="color: #fff; font-size: 18px;">17 centres en France · Ouverts 7j/7</p>
            <a href="/?p=free" class="btn btn-primary btn-lg"><i class="fa fa-gift"></i> 1ère séance offerte</a>
          </div>
        </div>
      </div>
    </li>
  </ul>
</section>
   <br />

<script>
if (typeof centersWithCalendly === "undefined") {
    var centersWithCalendly = [];
}
console.log("centersWithCalendly chargé :", centersWithCalendly);
</script>

<!-- Formulaire -->
              <h2 class="form-group" style="text-align: center;">🎁 Votre séance découverte OFFERTE (valeur 45€)</h2>
              <div class="col-md-6">
                <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
                  <p>Vous pouvez vous réserver sur notre <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank"><strong>calendrier</strong> (cliquez ici)</a> ou en prenant rendez-vous ci-dessous.</p>
                <?php endif; ?>
                <form role="form" class="contact-form" method="POST" action="_page.php">
                  <div class="form-group">
                    <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?</label>
                    <select class="form-control" id="center" name="center">
                      <?php foreach ($centers_list_d as &$free_d) { ?>
                        <option <?php if (isset($_GET['city']) &&  $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= $free_d['id'] ?>"><?= $free_d['city'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div id="calendrier_section" style="display: none; margin-top: 15px;">
   		 <p>Vous pouvez réserver sur notre  
   		 <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" style="color: #00acdc;"><strong>calendrier</strong> (cliquez ici)</a> 
		    ou en prenant rendez-vous ci-dessous.</p>
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
                  <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 15px;">
                    <i class="fa fa-gift"></i> JE RÉSERVE MA SÉANCE GRATUITE
                  </button>
                  <p style="text-align: center; color: #999; font-size: 12px; margin-top: 10px;">
                    ✓ Sans engagement · ✓ Réponse sous 24h · ✓ 100% gratuit
                  </p>
                </form>
              </div>
              <!-- Fin du formulaire -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const centerSelect = document.getElementById("center");
    const calendrierSection = document.getElementById("calendrier_section");

    centerSelect.addEventListener("change", function () {
        const selectedCenter = parseInt(this.value);
        const centersWithCalendly = [305, 347, 349];

        if (centersWithCalendly.includes(selectedCenter)) {
            calendrierSection.style.display = "block";
        } else {
            calendrierSection.style.display = "none";
        }
    });

    // Vérifier si un centre est déjà sélectionné au chargement
    if (centersWithCalendly.includes(parseInt(centerSelect.value))) {
        calendrierSection.style.display = "block";
    } else {
        calendrierSection.style.display = "none";
    }
});
</script>


