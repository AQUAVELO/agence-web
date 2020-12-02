<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Contact us</h1>

    <ol class="breadcrumb pull-right">
      <li><a href="#">Home</a></li>
      <li class="active">Contact us</li>
    </ol>
  </div>
</header>

<section class="content-area bg1">
  <div class="container">

    <header class="page-header text-center">
      <h1 class="page-title">Get in touch</h1>

      <h2>We’d love to hear from you</h2>

      <p class="larger">Have a question or need help? Use the form or contact us at <a href="mailto:hello@example.com">hello@example.com</a>.</p>
    </header>

    <div class="row">
      <div class="col-md-8">
        <div class="contactForm">
          <div class="successMessage alert alert-success alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Thank You! E-mail was sent.
          </div>
          <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Ups! An error occured. Please try again later.
          </div>

          <form class="liveForm" role="form" action="form/send.php" method="post" data-email-subject="Contact Form" data-show-errors="true" data-hide-form="false">
            <fieldset>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Name <span>(Required)</span></label>
                    <input type="text" required name="field[]" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email <span>(Required)</span></label>
                    <input type="email" required name="field[]" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Subject</label>
                    <input type="text" name="field[]" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Message <span>(Required)</span></label>
                    <textarea name="field[]" required class="form-control" rows="5"></textarea>
                  </div>
                </div>
              </div>
              <input type="submit" class="btn btn-primary" value="Send Message">
            </fieldset>
          </form>
        </div>
      </div>
      <div class="col-md-4">

        <dl>
          <dt>Address</dt>
          <dd>121 King Street, Melbourne</dd>
          <dd>Victoria 3000 Australia</dd>

          <dt>Phone Number</dt>
          <dd>+61 3 8376 6284</dd>

          <dt>Email</dt>
          <dd><a href="mailto:hello@example.com">hello@example.com</a></dd>
        </dl>


      </div>
    </div>

  </div>
</section>

<section class="content-area bg2" data-topspace="0" data-btmspace="0">
  <div class="mapOuter">
    <div class="googleMap" data-location="Junipers Blvd. 380 Queens, NY 11379, USA" data-height="410" data-offset="0"></div>
  </div>
</section>
