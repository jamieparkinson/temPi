/*
 * Little script that reads GPIO temperature from DS18B20 chip on pin 4
 * Specify "-save" to write to tempDB.db or "-return" to return the temp
*/

#include <iostream>
#include <sqlite3.h>
#include <string>
#include <fstream>
#include <stdlib.h>

using namespace std;

float temp;
char tempString[5];
int DBconn;

int main(int argc, char *argv[]) {
	ifstream tempFile("/sys/bus/w1/devices/28-0004431958ff/w1_slave"); // open GPIO sensor
	if (!tempFile.is_open()) return 0;
	tempFile.seekg(69, ios::beg); // The temperature is at this position
	tempFile.get(tempString, 6);
	temp = atof(tempString) / 1000.0;

	if ( string(argv[1]) == "-save" ) {
		sqlite3 *tempDB;
		sqlite3_stmt *stmt;
		DBconn = sqlite3_open("/home/pi/temPi/tempDB.db", &tempDB);
	
		string SQLstr = "INSERT INTO temps values(datetime('now'), " + to_string(temp) + ")";
		sqlite3_prepare(tempDB, SQLstr.c_str(),-1,&stmt,NULL);
		sqlite3_step(stmt);
		sqlite3_finalize(stmt);
		sqlite3_close(tempDB);
		return 0;
	} else if ( string(argv[1]) == "-return" ) {
		cout << temp;
		return 0;
	} else {
		cout << "please specify save or return option" << endl;
		return -1;
	}
}

