import java.awt.*;
import java.awt.event.*;
import java.beans.*;
import com.topaz.sigplus.*;

import javax.comm.*;
import java.io.*;

import javax.swing.*;
import com.sun.image.codec.jpeg.JPEGCodec;
import com.sun.image.codec.jpeg.JPEGImageEncoder;
import java.awt.image.BufferedImage;



public class LoadSigString extends Frame
   {
   SigPlus              sigObj = null;

   public static void main( String Args[] )
	  {
	  LoadSigString demo = new LoadSigString();
	  }

	public LoadSigString()
		{
		String drivername = "com.sun.comm.Win32Driver"; 
  		try 
  			{ 
                            CommDriver driver = (CommDriver) Class.forName(drivername).newInstance(); 
                            driver.initialize(); 
  			} 
		catch (Throwable th) 
			{
			/* Discard it */
			} 


		try
			{
			ClassLoader cl = (com.topaz.sigplus.SigPlus.class).getClassLoader();
	  		sigObj = (SigPlus)Beans.instantiate( cl, "com.topaz.sigplus.SigPlus" );

			setLayout( new GridLayout( 1, 1 ) );
			add( sigObj );
			pack();
			setTitle( "DemoSigPlus" );

			addWindowListener( new WindowAdapter()
				{
				public void windowClosing( WindowEvent we )
					{
					sigObj.setTabletState( 0 );
					System.exit( 0 );
					}

				public void windowClosed( WindowEvent we )
					{
					System.exit( 0 );
					}
				} );

			sigObj.addSigPlusListener( new SigPlusListener()
				{
				public void handleTabletTimerEvent( SigPlusEvent0 evt )
					{
					}

				public void handleNewTabletData( SigPlusEvent0 evt )
					{
					}

				public void handleKeyPadData( SigPlusEvent0 evt )
					{
					}
				} );


			setSize( 640, 256 );
			show();

			sigObj.setTabletModel( "SignatureGemLCD" );
			sigObj.setTabletComPort( "COM1" );
			sigObj.setSigCompressionMode(1);			
                        
                        sigObj.setDisplayJustifyMode(5);
                        sigObj.setDisplayJustifyX(5);
                        sigObj.setDisplayJustifyY(5);
                        
   
sigObj.setSigString("06003800B302F50200FF000001FF01FFFFFFFFFEFEFCFCFBFCFAF9FAF8F9F6FAF4FAF4FDF3FEF5FFF501F602F903FA04FE060008050A070D0B100D110E120E110E100C0E0A0D060A04080008FD07FA07F606F305F104F001F000F0FFF0FCF2FBF4F8F6F7F9F7FCF600F704F706F905FB03FE01FFFF000F00E702E102000200050109000C000DFF0E000E000E010E020D01090003000100FF0D00D802BB0200FB00F801F502F502F904FD0301040202030101000000003F004E030E03FFFFFF00FEFFFD00FC00FC01FC02FC04FB05FC07FB0AFA0BFA0EFB0FFB0EFD0EFF0B02090505070209FD09FB0AF708F508F306F303F403F501F700FA00FD00010104040A050F06150619051B031B0019FE17FC13FA10F90CF808F705F603F5FFF5FEF5FBF7F9F9F7FAF6FDF5FEF4FFF501F502F802FA01FE00000000ED008F0326030000000301050209010B020E010F010E010B010901060104030004FE04FA04F804F604F403F304F303F203F402F303F502F703F903FC02FF0302030403080309030C040C030B040C04090609060706050702070008FE07FC08F907F807F706F706F604F603F601F700F9FEFCFDFEFC01FA04FB07FA0AF90BF90DF90DFA0CFD0BFF08020503040501060006FD06FB06F906F806F705F604F703F802F901FB01FC0000000300060009010A010B010A030903070504050207FF08FD08FA07F807F606F506F404F403F203F002ED01EB01EA01EB00EC00F100F501F9FFFD0000FE03FE06FE0BFD10FE13FD17FE19FE19FF1801160314060F060B06080704060206FF07FD05FB06F905F704F604F502F502F401F600F701FA00FDFF000003FF06FF09FF0CFE0CFE0D000B00090108020603040503060106FE07FE05FB06FC07FA05FA05FA05FA03F902FA02FC02FC00FF0100FF04FF050006FF07000701070106010403040301050106FE08FC08FA0AF80BF80AF70BF609F707F806F904FB02FC02FF0101FF04FF06FE07FD08FF0800090107020604050604070109000BFE0BFC0CFA0BF90BF80AF807F804F802FA00FCFDFEFC02FA05FA08F90CFA0FFD11FF110210050C090A0C060F0210FE11FB10F80FF50AF704FC01FEFF011200D503CF0202FF07FE0EFB16F91EF726F72CF631F634F834F932F92BFC2300160207010202FEFF00000000000000010000000000000000000000007800000001000000000000000000");
			}
		catch ( Exception e )
			{
			return;
			}
		}	

   }
