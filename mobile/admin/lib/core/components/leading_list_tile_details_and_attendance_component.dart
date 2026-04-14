import 'package:flutter/material.dart';

class LeadingListTileDetailsAndAttendanceComponent extends StatelessWidget {
  const LeadingListTileDetailsAndAttendanceComponent({
    super.key,
    this.studentPhoto,
  });
  final String? studentPhoto;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      height: size.height * (isRotait ? 0.053 : 0.07),
      width: size.width * 0.09,
      child: ClipOval(
        child: Image.network(
          studentPhoto != null && studentPhoto!.isNotEmpty
              ? studentPhoto!
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
