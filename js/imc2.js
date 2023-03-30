
 var p = document.querySelectorAll('.jauge');
document.documentElement.style.setProperty('--taux', 220);
Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
    width = e.dataset.width;
    e.style.setProperty('width', 0);
    //colorize selon largeur du segment
    //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;
});




function calcularImc() {

    let body = document.querySelector('.interface');
    var formulario = document.getElementById("formulario");

    var kilos = +formulario.kilos.value;


    var centimetros = +formulario.centimetros.value;

    var altura = centimetros / 100;
    var altura = altura * altura;
    var imc = kilos / altura;

    if (imc <= 18) {
        var messageImc = ('Vous êtes en situation de minceur voir maigreur. Cela peut être en lien avec votre patrimoine génétique par exemple ou d’autres raisons. Ce qu’il vous faut préconiser : une prise de poids progressive, si vous rencontrez des difficultés pour y arriver consultez un diététicien ou votre médecin et pratiquez une activité dynamique tel que l\'Aquavelo pour renforcer votre systéme musculaire. Attention tout de même si vous avez plus de 60 ans, cela est peut être un signe de dénutrition, et à ce moment là il faut impérativement consulter votre médecin.');
        document.getElementById('interface').style.backgroundColor = "fdc0fd";
        Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
            width = e.dataset.width;
            e.style.setProperty('width', 35);
            //colorize selon largeur du segment
            //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;

        });
    } else if (imc <= 25) {
        var messageImc = ('Vous n\'êtes ni en surpoids ni trop maigre, vous êtes sur le bon poids. Il faut vous maintenir en continuant avec une alimentation équilibrée et variée et en pratiquant une activité physique régulière comme l\'Aquavelo.')
        document.getElementById('interface').style.backgroundColor = "eb8ceb";
        Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
            width = e.dataset.width;
            e.style.setProperty('width', 70);
            //colorize selon largeur du segment
            //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;
        });
    } else if (imc <= 30) {
        var messageImc = ('Vous êtes en surpoids. La perte de poids va être nécessaire pour éviter l’apparition de maladies associées comme le diabète, l’hypertension artérielle ou des troubles cardiaques. Concernant l’alimentation, il faut diminuer les graisses et consommer davantage de fruits et légumes. Il faut aussi débuter une activité physique comme l\'Aquavelo, consulter un diététicien ou votre médecin pour obtenir un bilan complet afin d\'obtenir la meilleure option pour perdre du poids.')
        document.getElementById('interface').style.backgroundColor = "fa43fa";
        Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
            width = e.dataset.width;
            e.style.setProperty('width', 100);
            //colorize selon largeur du segment
            //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;
        });
    } else if (imc <= 34) {
        var messageImc = ('Vous êtes en obésité. Il y a un danger pour votre santé. Plusieurs raisons qui sont peut être l’inactivité physique, l’alimentation trop riche, le patrimoine génétique, le mode de vie, le contexte ethnique et socio-économique, l’exposition à certains produits chimiques, certaines maladies et l’utilisation de certains médicaments. Cependant vous pouvez réagir seul(e) en démarrant une activité physique comme l\'Aquavelo, ou en consultant un diététicien ou un médecin.')
        document.getElementById('interface').style.backgroundColor = "d928d9";
        Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
            width = e.dataset.width;
            e.style.setProperty('width', 140);
            //colorize selon largeur du segment
            //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;
        });
    }
    else if (imc <= 80) {
        var messageImc = ('Vous êtes à un stade d’obésité avancé, qui est dangereux pour votre santé. La perte de poids est indispensable. Il est capital de perdre du poids rapidement. Vous êtes davantage exposé au risque d’AVC, et autres, il est donc urgent de consulter un diététicien ainsi que votre médecin pour obtenir un bilan complet. Vous pouvez démarrer une activité comme l\'Aquavélo de manière progressive.')
        document.getElementById('interface').style.backgroundColor = "cc0dcc";
        Array.from(document.querySelectorAll('.jauge')).forEach(function (e) {
            width = e.dataset.width;
            e.style.setProperty('width', 180);
            //colorize selon largeur du segment
            //e.style.setProperty('--taux', parseInt( 1 * width/2 * ( width/1.725 ))) ;
        });
    }




    formulario.imc.value = imc.toFixed();
    formulario.messageImc.value = messageImc.toLocaleString();
    document.getElementById('input').innerHTML = formulario.messageImc.value;


}



