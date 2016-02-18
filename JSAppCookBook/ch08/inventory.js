// Define a product constructor
function product(name, description, price, unit) {
	this.name = name;
	this.description = description;
	this.price = price;
	this.unit = unit;
	this.plu = name.substring(0, 3).toUpperCase() + parseInt(price).toString();
	this.icon = new Image();
	return this;
	}

// Define a category constructor
function category(name, description) {
	this.name = name;
	this.description = description;
	this.prodLine = eval(name);

	var imgDir = "images/" + name.toLowerCase() + "/";
	for (var i = 0; i < this.prodLine.length; i++) {
		this.prodLine[i].icon.src = imgDir + this.prodLine[i].name.toLowerCase() + ".gif";
		}
	return this; 
	}

// Define a function to generate the products 
// and then assign them to categories
function makeProducts() {
	Appliances = new Array(new product("Dryer", "Stylish pastel design, contemporary two-button engineering.", 263.37	, "each"), 
                new product("Hairdryer", "Fancy yellowish blast, and durable cord. No expense spared.", 1.15, "pair"), 
                new product("Oven", "Made in the 1850's, this coal-powered unit quickly blackens any favorite dish.", 865.78, "each"), 
                new product("Radio", "Revolutionary one-channel technology. White noise and static included.", 15.43, "each"), 
                new product("Toaster", "BBQ-style toaster. Only a moderate shock hazard.", 25.78, "each"),
                new product("Washer", "Does a great job on partially everything.", 345.61, "each")
                );

	Buildings = new Array(new product("Barn", "Complete with rusty silo and rotting doors. Pig sty sold separately.", 6350.57, "each"), 
               new product("Lighthouse", "Made of cement. Assorted light bulbs. Three AA batteries not included.", 12351.15, "each"), 
               new product("Igloo", "Made from top grade snow blocks, and includes a chimney and 5-ton air conditioning unit.", 954.76, "each"), 
               new product("City", "Buildings, streets, lights, skyline. Excellent volume purchase.", 334165.95, "each"), 
               new product("Castle", "Sturdy medieval design, complete with alligators in moat, and remote control drawbridge.", 93245.59, "each"),
               new product("Tower", "Really tall. Ideal for winning friends and spotting forest fires.", 24345.87, "pair")
               );

	Clothing = new Array(new product("Bowtie", "Swell red fabric. Doubles a bow for Christmas wreaths or birthday gifts.", 5.41, "five"), 
              new product("Necktie", "Be the first (and probably only) one (ever) on your block. Made of genuine burlap.", 1.15, "each"), 
              new product("Purse", "Attractive green material. Wards off most mammals.", 18.97, "each"), 
              new product("Jacket", "Plush fake fur with fiberglass lining. Washer safe.", 180.72, "each"), 
              new product("Glove", "Covers all four fingers and one thumb. Fancy latex design.", 6.59, "three"),
              new product("Dress", "Found at a garage sale. Also doubles as a picnic table cover.", 7.99, "each"), 
              new product("Watch", "Geuine replica. Doesn't tell time. You have to look at it.", 6.19, "each")
              );

	Electronics = new Array(new product("Camcorder", "Solar-powered. Free microphone. Custom-built for blackmailing close relatives.", 60.45, "each"), 
                 new product("Stereo", "Quadraphonic, pre 8-track sound. Leisure suit and roach killer shoes are optional", 54.91, "each"), 
                 new product("Speaker", "Extra piece of hi-fi junk. Works best if discarded.", 1.90, "each"), 
                 new product("Remote", "Dozens of buttons. Controls everything- TV, VCR, stereo, pets, local government.", 465.51, "each"), 
                 new product("Cellphone", "Product of tin can technology. 35-ft calling area. Dandy lavender plastic.", 64.33, "each"),
                 new product("Camera", "Takes brilliant one-color photos. Landfill safe.", 2.95, "each"), 
                 new product("Television", "Two-channel UHF only model. Wow.", 22.57, "each")
                 );

	Food = new Array(new product("Cheese", "Wait 'til you get a wiff. Puts bleu cheese to shame.", 3.05, "chunk"), 
          new product("Fries", "More grease than the local car garage. You can't beat the taste, though.", 1.15, "box"), 
          new product("Eggs", "The standard breakfast staple.", 1.07, "dozen"), 
          new product("Drumstick", "This leg of pterodactyl is a sure crowd pleaser.", 100.00, "half ton"), 
          new product("Chips", "Opened-bag flavor. Guaranteed stale, or your money back.", 1.59, "bag"),
          new product("Shrimp", "Great raw, served above room temperature.", 2.95, "each")
          );

	Hardware = new Array(new product("Chainsaw", "Be your own eager beaver with this tree-cutting machine.", 226.41, "each"), 
              new product("Cycle", "Mow down the wheat field with a few swipes. Just like the Grim Reaper's.", 11.15, "each"), 
              new product("Hammer", "Tempered steel head, fiberglass handle. Perfect for hitting things.", 9.87, "each"), 
              new product("Lawnmower", "Self-propelled (you propel it yourself).", 165.95, "each"), 
              new product("Pliers", "Perfect for eye brows and nose hairs.", 6.59, "each"),
              new product("Stake", "This 2-in-1 miracle secure tents or gets rid of vampires.", 3.95, "pair")
              );

	Music = new Array(new product("Bongos", "Great little noise makers for even the most sophisticated occasions.", 35.50, "bongo"), 
           new product("Piano", "It ain't grand, but this baby will make you sound like tavern material in no time.", 1001.40, "each"), 
           new product("Notes", "Choose from A, B, C, D, E, F, or G. Can be reused in any song.", 2.97, "note"), 
           new product("Guitar", "Strum, strum. This one is your fast track to fame and fortune.", 241.11, "each"), 
           new product("Trumpet", "Solid copper body, and not many dents. Extra spit valve included.", 683.59, "each")
           );

	categorySet = new Array(new category("Appliances", "Kitchen machines to make life easier"),
					  new category("Buildings", "Architectural structures your can't resist"),
 					  new category("Clothing", "Fashionably questionable apparel for the 21st century"),
					  new category("Electronics", "Nifty gizmos that drain your wallet"),
					  new category("Food", "The best product to order over the Net"),
                 new category("Hardware", "All kinds of general purpose construction tools"),
					  new category("Music", "The hottest new instruments from places you've never heard of")
 					  );
	}		