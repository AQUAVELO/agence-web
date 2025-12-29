  <section class="flexslider std-slider" data-height="740" data-loop="true" data-smooth="false" data-slideshow="true" data-speed="15000" data-animspeed="550" data-controls="true" data-dircontrols="true">
  <ul class="slides">
    <li data-bg="/images/content/home-v1-slider-03.webp" class="img-fluid" style="background-position: top; background-size: cover; min-height: 740px !important;">
      <div class="container">
        <div class="inner">
          <div class="row">
            <div class="col-md-6 pull-left animated" data-fx="fadeIn" style="width: 100%; text-align: center; margin-top:0;">
              

              <div style="margin-top: 180px; text-align: center;">
                <h2 style="color: #fff; font-size: 48px; text-shadow: 2px 2px 8px rgba(0,0,0,0.8); margin-bottom: 15px; font-weight: 700;">
                  Vitalité, Minceur, Bien-être
                </h2>
                <p style="color: #fff; font-size: 22px; text-shadow: 1px 1px 4px rgba(0,0,0,0.7); margin-bottom: 30px;">
                  <strong>30 min = 500 calories brûlées</strong> · Dans l'eau à 29°C
                </p>
                <a href="/?p=free" class="btn btn-lg" style="background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff; padding: 18px 40px; font-size: 20px; border-radius: 50px; text-decoration: none; display: inline-block; box-shadow: 0 5px 25px rgba(255,107,53,0.5); animation: pulse-glow 2s ease-in-out infinite;">
                  <i class="fa fa-gift"></i> MA SÉANCE GRATUITE
                </a>
                <p style="color: #fff; font-size: 14px; margin-top: 15px; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                  ✓ Sans engagement · ✓ Places limitées
                </p>
              </div>
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
              <h2 class="form-group" style="text-align: center;">🎁 Votre séance découverte OFFERTE</h2>
              <div class="col-md-6">
                <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
                  <p>Vous pouvez vous réserver sur notre <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank"><strong>calendrier</strong> (cliquez ici)</a> ou en prenant rendez-vous ci-dessous.</p>
                <?php endif; ?>
                <form role="form" class="contact-form" id="sliderForm" method="POST" action="/?p=free">
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
                  <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-slider" value="">
                  <button type="submit" id="submitBtnSlider" class="btn btn-lg" style="width: 100%; padding: 18px; background: linear-gradient(135deg, #ff6b35, #f7931e); border: none; color: #fff; font-size: 18px; font-weight: 700; border-radius: 50px; box-shadow: 0 5px 20px rgba(255,107,53,0.4); cursor: pointer; transition: all 0.3s ease;">
                    <i class="fa fa-gift"></i> OUI, JE VEUX MA SÉANCE GRATUITE !
                  </button>
                  <p style="text-align: center; color: #999; font-size: 12px; margin-top: 10px;">
                    ✓ Sans engagement · ✓ Réponse sous 24h · ✓ 100% gratuit
                  </p>
                </form>
              </div>
              <!-- Fin du formulaire -->
<?php if ($settings['recaptcha_enabled']) : ?>
<!-- ⭐ reCAPTCHA v3 Script -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($settings['recaptcha_site_key']); ?>"></script>
<?php endif; ?>

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

    // ⭐ reCAPTCHA v3 pour le formulaire slider
    const sliderForm = document.getElementById('sliderForm');
    if (sliderForm) {
        sliderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtnSlider');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ENVOI...';
            
            var recaptchaEnabled = <?= $settings['recaptcha_enabled'] ? 'true' : 'false'; ?>;
            var recaptchaSiteKey = "<?= htmlspecialchars($settings['recaptcha_site_key'], ENT_QUOTES, 'UTF-8'); ?>";
            
            if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                grecaptcha.ready(function() {
                    grecaptcha.execute(recaptchaSiteKey, {action: 'submit_free_trial'}).then(function(token) {
                        document.getElementById('g-recaptcha-response-slider').value = token;
                        sliderForm.submit();
                    }).catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        sliderForm.submit();
                    });
                });
            } else {
                sliderForm.submit();
            }
        });
    }
});
</script>


