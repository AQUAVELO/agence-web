document.getElementById('getAdviceButton').addEventListener('click', async () => {
    const responseDiv = document.getElementById('response');
    responseDiv.textContent = 'Chargement...';

    try {
        const data = await getAdvice();
        responseDiv.textContent = data.choices[0].message.content;
    } catch (error) {
        responseDiv.textContent = 'Une erreur s\'est produite. Veuillez réessayer.';
        console.error(error);
    }
});
