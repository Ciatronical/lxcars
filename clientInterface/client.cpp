#include <iostream>
#include <fstream>
#include <string>
using namespace std;

//HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
int main( int argc, char* argv[] ){

    if( argc != 2 ){
        cerr << "Error: Number of arguments false." << endl;
        return -1;
    }

  string param = argv[1];
    string command = param.substr( 0, 3 );
    string data = param.substr( 3 );

    ofstream outfile;
    outfile.open( "control" );

  if( command == "kba" ){
      cout << "KBA" << endl;
      outfile << "<Commands> <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=" << data << " /> </Args></Command> </Commands>" <<endl;
     //cout << "data: " << data << endl;
     //cout << "hsn: " << data.substr( 0, 4 ) << "tsn: " << data.substr( 5 ) << endl;

  }





  //cout << "argc: " << argc << endl;
    //cout << "param: " << param << endl;
    //cout << "command: " << command << endl;
  return 0;
}
