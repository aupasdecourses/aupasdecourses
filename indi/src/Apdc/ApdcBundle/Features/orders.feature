Feature: Get orders from 2018-01-01 to 2018-01-31

	Scenario: Display all orders from 1st january to 31rd january 2018

		Given I am on "/login"
		When I fill in "_username" with "sturquier"
		And I fill in "_password" with "sturquier"
		And I press "Connexion"
		Then I should see "Back office Au Pas De Courses"

		Given I am on "/"
		When I follow "Dispatch APDC"
		And I follow "Toutes les commandes"
		And I fill in "from_to[from]" with "2018-01-01"
		And I fill in "from_to[to]" with "2018-01-31"
		And I press "Search"
		Then I should see "Liste des commandes par client"
