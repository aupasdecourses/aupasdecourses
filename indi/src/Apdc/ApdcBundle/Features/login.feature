Feature: Login

	Scenario: Indi login
		Given I am on "/login"
		When I fill in "_username" with "sturquier"
		And I fill in "_password" with "sturquier"
		And I press "Connexion"
		Then I should see "Bienvenue sur la plateforme de gestion des commandes et livraisons de Au Pas De Courses"