<?php

/**
 * Use this file to output reports required for the SQL Query Design test.
 * An example is provided below. You can use the `asTable` method to pass your query result to,
 * to output it as a styled HTML table.
 */

//$database = 'nba2019';
//require_once('vendor/autoload.php');
require_once('include/utils.php');

/*
 * Example Query
 * -------------
 * Retrieve all team codes & names
 */
echo '<h1>Example Query</h1>';
$teamSql = "SELECT * FROM team";
$teamResult = query($teamSql);
// dd($teamResult);
echo asTable($teamResult);

/*
 * Report 1
 * --------
 * Produce a query that reports on the best 3pt shooters in the database that are older than 30 years old. Only 
 * retrieve data for players who have shot 3-pointers at greater accuracy than 35%.
 * 
 * Retrieve
 *  - Player name
 *  - Full team name
 *  - Age
 *  - Player number
 *  - Position
 *  - 3-pointers made %
 *  - Number of 3-pointers made 
 *
 * Rank the data by the players with the best % accuracy first.
 */
echo '<h1>Report 1 - Best 3pt Shooters</h1>';
// write your query here
$report1Sql = "SELECT roster.name as player_name, team.name as full_team_name, player_totals.age as age, roster.number as player_number, roster.pos as position,
                CONCAT(ROUND(player_totals.3pt * 100 / player_totals.3pt_attempted,2), '%') as 3pt_made_percentage, player_totals.3pt as number_3pt_made
                FROM player_totals LEFT JOIN roster ON player_totals.player_id = roster.id  LEFT JOIN team ON roster.team_code = team.code WHERE age > 30 HAVING 3pt_made_percentage > 35";
$report1Result = query($report1Sql);
// dd($report1Result);
echo asTable($report1Result);

/*
 * Report 2
 * --------
 * Produce a query that reports on the best 3pt shooting teams. Retrieve all teams in the database and list:
 *  - Team name
 *  - 3-pointer accuracy (as 2 decimal place percentage - e.g. 33.53%) for the team as a whole,
 *  - Total 3-pointers made by the team
 *  - # of contributing players - players that scored at least 1 x 3-pointer
 *  - of attempting player - players that attempted at least 1 x 3-point shot
 *  - total # of 3-point attempts made by players who failed to make a single 3-point shot.
 * 
 * You should be able to retrieve all data in a single query, without subqueries.
 * Put the most accurate 3pt teams first.
 */
echo '<h1>Report 2 - Best 3pt Shooting Teams</h1>';
// write your query here
$report2Sql = "SELECT team.name as team_name, CONCAT(ROUND(SUM(player_totals.3pt) * 100 / SUM(player_totals.3pt_attempted),2), '%') as 3pt_made_percentage, SUM(player_totals.3pt) as number_3pt_made,
                CASE 
                    WHEN COUNT(player_totals.3pt) > 0 THEN COUNT(player_totals.3pt) 
                    ELSE NULL END
                as contributing_players,
                CASE 
                    WHEN COUNT(player_totals.3pt_attempted) > 0 THEN COUNT(player_totals.3pt_attempted) 
                    ELSE NULL END
                as attempted_players,
                CASE 
                    WHEN player_totals.3pt < 1 AND player_totals.3pt_attempted > 0 THEN SUM(player_totals.3pt_attempted) 
                    ELSE 0 END
                as total_attempted_shots
                FROM player_totals LEFT JOIN roster ON player_totals.player_id = roster.id  LEFT JOIN team ON roster.team_code = team.code GROUP BY roster.team_code ORDER BY 3pt_made_percentage DESC";
$report2Result = query($report2Sql);
// dd($report2Result);
echo asTable($report2Result);

?>