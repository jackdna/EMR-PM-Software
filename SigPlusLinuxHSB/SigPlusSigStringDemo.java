import java.awt.*;
import java.awt.event.*;
import java.beans.*;
import com.topaz.sigplus.*;
import gnu.io.*;
import java.io.*;
/*import javax.swing.*;*/

public class SigPlusSigStringDemo extends Frame implements Runnable
   {
   SigPlus              sigObj = null;
   Thread               eventThread;
   String               mySigString;

   public static void main( String Args[] )
	  {
	  SigPlusSigStringDemo demo = new SigPlusSigStringDemo();
	  demo.setSize(800,300);
	  demo.setVisible(true);
          demo.setBackground(Color.lightGray);
	  }

	public SigPlusSigStringDemo()
		{
                GridBagLayout gbl = new GridBagLayout();
		GridBagConstraints gc = new GridBagConstraints();
		setLayout(gbl);
		Panel controlPanel = new Panel();
		setConstraints(controlPanel, gbl, gc, 0, 0,
		GridBagConstraints.REMAINDER, 1, 0, 0,
		GridBagConstraints.CENTER,
		GridBagConstraints.NONE,0, 0, 0, 0);
		add(controlPanel, gc);



		controlPanel.add(connectionChoice);
		controlPanel.add(connectionTablet);
		
		Button startButton = new Button("START");
		controlPanel.add(startButton);

		Button stopButton = new Button("STOP");
		controlPanel.add(stopButton);
		
		Button clearButton = new Button("CLEAR");
		controlPanel.add(clearButton);

		Button saveSigButton = new Button("SAVE SIGSTRING");
		controlPanel.add(saveSigButton);

		Button loadSigButton = new Button("LOAD SIGSTRING");
		controlPanel.add(loadSigButton);

                controlPanel.add(txtPath);

		Button okButton = new Button("QUIT");
		controlPanel.add(okButton);

		initConnection();





		try
			{
			ClassLoader cl = (com.topaz.sigplus.SigPlus.class).getClassLoader();
	  		sigObj = (SigPlus)Beans.instantiate( cl, "com.topaz.sigplus.SigPlus" );




		setConstraints(sigObj, gbl, gc, 0, 1,
		GridBagConstraints.REMAINDER, 1, 1, 1,
		GridBagConstraints.CENTER,
		GridBagConstraints.BOTH, 5, 0, 5, 0);
		add(sigObj, gc);
		sigObj.setSize(100,100);
                sigObj.clearTablet();
		setTitle( "Demo SigPlus Application" );

	   okButton.addActionListener(new ActionListener(){
		  public void actionPerformed(ActionEvent e){
			    sigObj.setTabletState(0);
    		            System.exit(0);
		   }
	  });

	   startButton.addActionListener(new ActionListener(){
		  public void actionPerformed(ActionEvent e){
			    sigObj.setTabletState(0);
			    sigObj.setTabletState(1);
		   }
	  });

	  stopButton.addActionListener(new ActionListener(){
		  public void actionPerformed(ActionEvent e){
			    sigObj.setTabletState(0);
		   }
	  });

	  clearButton.addActionListener(new ActionListener(){
		  public void actionPerformed(ActionEvent e){
			    sigObj.clearTablet();
                        if(connectionTablet.getSelectedItem() == "SigGemColor57"){
                           sigObj.lcdRefresh(0, 0, 0, 640, 480);
                           sigObj.setLCDCaptureMode(2);
                        }
                            
		   }
	  });

	  saveSigButton.addActionListener(new ActionListener(){
	     public void actionPerformed(ActionEvent e){
		   sigObj.autoKeyStart();
		   sigObj.setAutoKeyData("Sample Encryption Data");
		   sigObj.autoKeyFinish();
                   sigObj.setEncryptionMode(2);
                   sigObj.setSigCompressionMode(1);
	           mySigString = sigObj.getSigString();
                   sigObj.clearTablet(); //clear signature
                   //reset SigPlus
                   sigObj.setSigCompressionMode(0);
                   sigObj.setEncryptionMode(0);
                   sigObj.setKeyString("0000000000000000");
                        if(connectionTablet.getSelectedItem() == "SigGemColor57"){
                           sigObj.lcdRefresh(0, 0, 0, 640, 480);
                           sigObj.setLCDCaptureMode(2);
                        }
              }
	  });


	  loadSigButton.addActionListener(new ActionListener(){
	     public void actionPerformed(ActionEvent e){

                if(mySigString != "")
                {
 	           sigObj.autoKeyStart();
		   sigObj.setAutoKeyData("Sample Encryption Data");
		   sigObj.autoKeyFinish();
                   sigObj.setEncryptionMode(2);
                   sigObj.setSigCompressionMode(1);
		   sigObj.setSigString(mySigString);
                if (sigObj.numberOfTabletPoints() > 0)
                     {
                    System.out.println("Signature returned successfully");
                     }
                 }
                 else
                 {
                    System.out.println("Signature not returned successfully!");
		 }
	      }
	  });


          //txtPath.addTextListener(new TextListener(){
		  //public void textValueChanged(TextEvent e){
                            //System.out.println(txtPath.getText());
		  //}
	  //});



	 connectionTablet.addItemListener(new ItemListener(){
		  public void itemStateChanged(ItemEvent e){
			    
                        if(connectionTablet.getSelectedItem() != "SignatureGemLCD4X3"){
                           sigObj.setTabletModel(connectionTablet.getSelectedItem());
                        }
                        else{
                           sigObj.setTabletModel("SignatureGemLCD4X3New"); //properly set up LCD4X3
                        }

                        if(connectionTablet.getSelectedItem() == "SigGemColor57"){
                           sigObj.setTabletBaudRate(115200);
                           sigObj.lcdRefresh(0, 0, 0, 640, 480);
                           sigObj.setLCDCaptureMode(2);
                        }
                     
		  }
	  });


	 connectionChoice.addItemListener(new ItemListener(){
		  public void itemStateChanged(ItemEvent e){
			    
                        if(connectionChoice.getSelectedItem() != "HSB"){
  	                   sigObj.setTabletComPort(connectionChoice.getSelectedItem());
                        }
                        else{
                           sigObj.setTabletComPort("HID1"); //properly set up HSB tablet
                        }
                            
		  }
	  });

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


			show();
                        
                        sigObj.setTabletModel("SignatureGem1X5");
                        sigObj.setTabletComPort("COM1");
 

			eventThread = new Thread(this);
			eventThread.start();

			}
		catch ( Exception e )
			{
			return;
			}
			
		}


                public void run()
	        {
	        try
		   {
		   while ( true )
			{
			Thread.sleep(100);
			}
		   }
	        catch (InterruptedException e)
		   {
		   }
	        }

                TextField txtPath = new TextField("C:\\test.sig", 30);
      
                Choice connectionChoice = new Choice();   protected String[] connections = 
	        {
		   "COM1", 
		   "COM2", 
		   "COM3", 
		   "COM4",
                   "USB", 
		   "HSB",  
	        };


                Choice connectionTablet = new Choice();   protected String[] tablets = 
	        {
                   "SignatureGem1X5",
                   "SignatureGem4X5",
      		   "SignatureGemLCD",
       		   "SignatureGemLCD4X3",
      		   "ClipGem",
      		   "ClipGemLGL",
                   "SigGemColor57"
	        };


                private void initConnection()
	        {
		   for(int i = 0; i < connections.length; i++)
		   {
			connectionChoice.add(connections[i]);
		   }

		   for(int i = 0; i < tablets.length; i++)
		   {
			connectionTablet.add(tablets[i]);
		   }

	        }

                //Convenience method for GridBagLayout
	        private void setConstraints(
		Component comp,
		GridBagLayout gbl,
	    	GridBagConstraints gc,
	    	int gridx,
	    	int gridy,
	    	int gridwidth,
	    	int gridheight,
	    	int weightx,
	    	int weighty,
	    	int anchor,
	    	int fill,
	    	int top,
	    	int left,
	    	int bottom,
	    	int right)
	    	{
			gc.gridx = gridx;
			gc.gridy = gridy;
			gc.gridwidth = gridwidth;
			gc.gridheight = gridheight;
			gc.weightx = weightx;
			gc.weighty = weighty;
			gc.anchor = anchor;
			gc.fill = fill;
			gc.insets = new Insets(top, left, bottom, right);
			gbl.setConstraints(comp, gc);
	    	}
            }

