import 'package:flutter/material.dart';

class CustomImageForTeacherInDetailsCardHomeView extends StatelessWidget {
  const CustomImageForTeacherInDetailsCardHomeView({
    super.key,
    required this.imageUrl,
  });
  final String? imageUrl;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return SizedBox(
      height: size.height * (isRotait ? 0.028 : 0.018),
      width: size.width * (isRotait ? 0.045 : 0.03),
      child: ClipOval(
        child: Image.network(
          imageUrl != null && imageUrl!.isNotEmpty
              ? imageUrl!
              : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
          fit: BoxFit.fill,
          errorBuilder: (context, error, stackTrace) {
            return Image.network(
              'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
              fit: BoxFit.fill,
            );
          },
        ),
      ),
    );
  }
}
