#include <iostream>
#include <fstream>
#include <string>
//#include <windows.h>
using namespace std;

// g++ -o LxCarsClient.exe client.cpp
// C:\Users\work\AppData\Roaming\DVSE GmbH\COPARTS Online
// HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
int main( int argc, char* argv[] ){

    if( argc != 2 ){
        cerr << "Error: Number of arguments false." << endl;
        return -1;
    }
	
	string param   = argv[1];
	string comdata = param.substr( 7 );
    string command = comdata.substr( 0, 3 );
    string data = comdata.substr( 3 );

    ofstream outfile;
	//ofstream debug;
    outfile.open( "Controlfile.cf" );
	
	//debug.open( "debug.txt" );
	//debug << "1" << endl;

	if( command == "kba" ){
		//cout << "KBA" << endl;
		//outfile << "<Commands> <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"\" /> </Args></Command> </Commands>" <<endl;
		// << "<Commands> <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"" << data << "\" /> </Args></Command> </Commands>" <<endl;
		outfile << "<Commands> <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"" << data << "\" /> </Args></Command> </Commands>" <<endl;
		//debug << "kba" << endl;
	}	
	
	//Sleep( 1000 );

	return 0;
}
