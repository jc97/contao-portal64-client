# Contao client for Portal64.de (chess league management system in Germany)

Developed by Julian Knorr.

## Synopsis

This is an extension for the Contao Open Source CMS.
It allows importing teams from the chess league management system "portal64" (see http://portal64.de/ (german)) into Contao. 
The events (rounds) of the team can thereby be imported into the calendar of Contao.
The line-ups and scores of teams can be included in front end by using the provided content elements.
This extension allows automatically updating the teams using the cron jobs of Contao.    

## Motivation

Many regional chess associations in Germany are using the application "portal64.de" to manage and publish the line-ups and scores of teams and matches. 
So this extension allows to import the published data into Contao instances to publish this data on own websites, e.g. on the website of a chess club.
In addition automatically importing the events of teams in different calendars is useful to centrally publish events related to an organisation on their website.    

## Dependencies and languages

This extension was developed and tested for Contao 3.5.x.
Probably it is possible to install it under Contao 4 too.

Furthermore this extension depends on the calendar extension for Contao.

Features according to the determination of player's ELO values and FIDE titles depend on the Contao extension "dwz", developed by the author of this extension.

This extension supports english and german language in back end and front end.  

## Installation

To install this extension copy it's files to `system/modules/portal64.de_client/`. 
After that you have to run database update in Contao. 

## Configuration and use

Configure this extension by setting the URL to the start page of the league management system in the settings of Contao.
This url normally only consist of the domain and protocol (http / https).

After that you can add terms and teams using the menu "teams" and publish data using the content elements.  

### Wildcards

The following wildcards are supported for headlines and titles of events:

| Wildcard   | Meaning                   |
|:----------:|---------------------------|
| \_N\_      | Official name of team     |
| \_I\_      | Internal name of team     |
| \_L\_      | League of team            |
| \_Y\_      | Start year of term        |
| \_T\_      | Term (YYYY/YYYY)          |
| \_LN\_     | Lot number of team        |
| \_TID\_    | Tournament id of league   |
| \_R\_      | Round \*                  |
| \_G\_      | Guest team \*             |
| \_H\_      | Home team \*              |

\* available for events only

### Custom Templates

This extension is very flexible regarding custom templates.
All settings of the respective content element, all player data and all data of rounds are provided in variables and arrays for templates. 

## Troubleshooting

Directly here on GitHub.