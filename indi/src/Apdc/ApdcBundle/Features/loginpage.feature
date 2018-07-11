Feature: LoginPage
	Test de la page de login d'Indi

	Scenario: La page de login affiche bien le titre "Connexion" & le formulaire de login
	Given I am on "/login"
	Then I should see "Connexion"