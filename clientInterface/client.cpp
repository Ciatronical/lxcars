#include <iostream>
#include <fstream>
#include <string>
using namespace std;


int main( int argc, char* argv[] ){
	
	if( argc != 2 ){
		cerr << "Argument Error" << endl;
		return -1;
	}
	
	ofstream outfile;
	outfile.open( "output.txt" );
	
	string param = argv[1];
	string command = param.substr( 0, 3 );
	outfile << "argv[1]: " << argv[1] << endl;
	
    cout << "argc: " << argc << endl;
	cout << "param: " << param << endl;
	cout << "command: " << command << endl;
    return 0;
}
