import java.io.File;

public class LZW_Launcher {
	
	public static void main(String[] args) {
		try {
			LZW lzw = new LZW(new File("input.txt"));
			lzw.code(new File("output.txt"));
		} catch (Exception e) {
			System.err.println("[ERROR] : " + e.getMessage());
			System.exit(1);
		}
	}
}
